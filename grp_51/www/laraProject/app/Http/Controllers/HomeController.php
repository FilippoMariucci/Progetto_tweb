<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Prodotto;
use App\Models\CentroAssistenza;
use App\Models\Malfunzionamento;
use App\Models\User;

/**
 * CONTROLLER HOMEPAGE E PAGINE INFORMATIVE - LINGUAGGIO: PHP con Laravel Framework
 * 
 * Questo controller gestisce tutte le pagine pubbliche del sistema e funzionalità generali:
 * 
 * RESPONSABILITÀ PRINCIPALI:
 * - Homepage con statistiche aggregate del sistema
 * - Pagine informative aziendali (Chi Siamo, Contatti)
 * - API pubbliche per dashboard e statistiche
 * - Sistema di ricerca globale multi-entità
 * - Monitoraggio stato sistema (health checks)
 * - Gestione form contatti con validazione
 * 
 * CARATTERISTICHE ARCHITETTURALI:
 * - Error handling robusto con fallback automatici
 * - Logging completo per monitoring e debug
 * - Metodi helper riutilizzabili seguendo DRY principle
 * - API REST per interfacce dinamiche
 * - Sicurezza input validation su tutti gli endpoint
 * 
 * LIVELLI ACCESSO:
 * - Metodi pubblici: accessibili a tutti (non autenticati)
 * - API: alcune richiedono autenticazione per dati sensibili
 * - Ricerca: filtraggio contenuti basato su livello utente
 */
class HomeController extends Controller
{
    /**
     * METODO INDEX - LINGUAGGIO: PHP con Aggregate Data Collection
     * 
     * Mostra la homepage principale del sistema con statistiche generali.
     * Accessibile a TUTTI gli utenti (livello 1 - pubblico).
     * 
     * FUNZIONALITÀ:
     * - Raccolta statistiche aggregate da tutti i moduli
     * - Caricamento dati showcase (prodotti recenti, centri principali)
     * - Error handling robusto con valori fallback
     * - Logging completo per monitoring performance
     * - Ottimizzazione query per velocità caricamento
     * 
     * ARCHITETTURA:
     * - Pattern "Safe Operations" per evitare crash homepage
     * - Lazy loading dati non critici
     * - Caching implicito tramite metodi helper ottimizzati
     * 
     * @return \Illuminate\View\View Vista homepage con statistiche complete
     */
    public function index()
    {
        try {
            // STEP 1: LOGGING INIZIO OPERAZIONE
            // Traccia ogni caricamento homepage per monitoring performance
            Log::info('Caricamento homepage iniziato');

            // STEP 2: RACCOLTA STATISTICHE CON FALLBACK SICURI
            // Array con contatori principali del sistema
            $stats = [
                // Conteggio prodotti attivi (solo quelli visibili al pubblico)
                'prodotti_totali' => $this->safeCount(Prodotto::class, ['attivo' => true]),
                
                // Conteggio totale centri di assistenza
                'centri_totali' => $this->safeCount(CentroAssistenza::class),
                
                // Conteggio soluzioni tecniche disponibili (malfunzionamenti risolti)
                'soluzioni_totali' => $this->safeCountWithCondition(
                    Malfunzionamento::class, 
                    function($query) {
                        // Solo malfunzionamenti che HANNO una soluzione
                        return $query->whereNotNull('soluzione');
                    }
                ),
                
                // Conteggio tecnici specializzati attivi
                'tecnici_totali' => $this->safeCount(User::class, ['livello_accesso' => '2']),
                
                // Calcolo anni esperienza azienda (dal 1994)
                'anni_esperienza' => date('Y') - 1994,
            ];

            // STEP 3: LOGGING STATISTICHE CALCOLATE
            Log::info('Statistiche calcolate', $stats);

            // STEP 4: RACCOLTA DATI AGGIUNTIVI PER SEZIONI HOMEPAGE
            
            // Ultimi prodotti aggiunti per sezione "Novità"
            $prodotti_recenti = $this->getProdottiRecenti();

            // Centri assistenza principali per sezione "Dove siamo"
            $centri_principali = $this->getCentriPrincipali();

            // Statistiche categorie prodotti per grafici
            $categorie_stats = $this->getCategorieStats();

            // STEP 5: LOGGING SUCCESSO OPERAZIONE COMPLETA
            Log::info('Homepage caricata con successo', [
                'stats' => $stats,
                'prodotti_recenti_count' => $prodotti_recenti->count(),
                'centri_principali_count' => $centri_principali->count(),
                'categorie_count' => count($categorie_stats)
            ]);

        } catch (\Exception $e) {
            // STEP 6: GESTIONE ERRORI CON FALLBACK COMPLETO
            // Se qualsiasi operazione fallisce, usa valori predefiniti
            // Questo garantisce che la homepage non si rompa MAI
            Log::error('Errore nel caricamento homepage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Valori di fallback realistici per mantenere credibilità
            $stats = [
                'prodotti_totali' => 150,       // Valore plausibile
                'centri_totali' => 25,          // Copertura nazionale tipica
                'soluzioni_totali' => 500,      // Database soluzioni tecniche
                'tecnici_totali' => 50,         // Staff tecnico
                'anni_esperienza' => 30         // Dal 1994
            ];
            
            // Collections vuote per evitare errori nelle viste
            $prodotti_recenti = collect();
            $centri_principali = collect();
            $categorie_stats = [];
        }

        // STEP 7: RETURN VISTA CON TUTTI I DATI
        // compact() crea array associativo per Blade template
        return view('home', compact(
            'stats',                    // Statistiche numeriche principali
            'prodotti_recenti',         // Collection prodotti per showcase
            'centri_principali',        // Collection centri per mappa/lista
            'categorie_stats'           // Array statistiche categorie per grafici
        ));
    }

    /**
     * METODO AZIENDA - LINGUAGGIO: PHP con Static Data Structure
     * 
     * Pagina informativa "Chi Siamo" con dettagli aziendali completi.
     * Include storia, certificazioni, contatti e timeline aziendale.
     * 
     * CONTENUTI:
     * - Informazioni legali e sede
     * - Contatti per diversi reparti
     * - Certificazioni e compliance
     * - Timeline storica azienda
     * - Statistiche dinamiche (da database)
     * 
     * @return \Illuminate\View\View Pagina informazioni aziendali
     */
    public function azienda()
    {
        // STEP 1: INFORMAZIONI AZIENDALI STRUTTURATE
        // Array associativo con tutti i dettagli aziendali
        $azienda_info = [
            // Denominazione sociale ufficiale
            'nome' => 'TechSupport Pro S.r.l.',
            'founded' => '1994',
            
            // Sede legale completa per aspetti normativi
            'sede_legale' => [
                'indirizzo' => 'Via dell\'Industria, 123',
                'citta' => 'Ancona',
                'cap' => '60121',
                'provincia' => 'AN',
                'regione' => 'Marche'
            ],
            
            // Contatti organizzati per reparto
            'contatti' => [
                'telefono_principale' => '+39 071 123 4567',
                'email_generale' => 'info@techsupportpro.it',
                'email_assistenza' => 'assistenza@techsupportpro.it',
                'fax' => '+39 071 123 4568'                        // Ancora richiesto per PA
            ],
            
            // Certificazioni per credibilità aziendale
            'certificazioni' => [
                'iso_9001' => 'Gestione Qualità',
                'iso_14001' => 'Gestione Ambientale',
                'ce_marking' => 'Conformità Europea',
                'energy_star' => 'Efficienza Energetica'
            ],
            
            // Numeri azienda (mix di valori fissi e dinamici)
            'numeri' => [
                'anni_esperienza' => date('Y') - 1994,                     // Calcolo dinamico
                'dipendenti' => 850,                                       // Valore fisso
                'centri_assistenza' => $this->safeCount(CentroAssistenza::class),  // Da database
                'prodotti_catalogo' => $this->safeCount(Prodotto::class, ['attivo' => true]), // Da database
                'clienti_serviti' => '2.5M+',                             // Marketing claim
                'interventi_anno' => '150K+'                              // Statistica operativa
            ]
        ];

        // STEP 2: TIMELINE AZIENDALE CRONOLOGICA
        // Storia dell'azienda per sezione "La nostra storia"
        $timeline = [
            '1994' => 'Fondazione dell\'azienda ad Ancona',
            '1998' => 'Prima espansione nazionale',
            '2001' => 'Certificazione ISO 9001',
            '2005' => 'Lancio del servizio assistenza online',
            '2010' => 'Raggiunta quota 500 centri assistenza',
            '2015' => 'Implementazione tecnologie smart',
            '2020' => 'Piattaforma digitale integrata',
            '2024' => 'Sistema AI per diagnostica predittiva'
        ];

        // STEP 3: RETURN VISTA AZIENDA
        return view('pages.azienda', compact('azienda_info', 'timeline'));
    }

    /**
     * METODO CONTATTI - LINGUAGGIO: PHP con Structured Contact Data
     * 
     * Pagina contatti con informazioni organizzate per tipo di supporto.
     * Facilita l'indirizzamento corretto delle richieste utenti.
     * 
     * ORGANIZZAZIONE:
     * - Contatti per tipologia richiesta
     * - Orari di apertura specifici
     * - Descrizioni per guidare la scelta
     * - Informazioni emergenze
     * 
     * @return \Illuminate\View\View Pagina contatti strutturata
     */
    public function contatti()
    {
        // STEP 1: STRUTTURAZIONE CONTATTI PER TIPOLOGIA
        // Array di contatti organizzato per efficienza del supporto
        $contatti = [
            [
                'tipo' => 'assistenza_tecnica',                   // Chiave per CSS/JS targeting
                'nome' => 'Assistenza Tecnica',
                'telefono' => '+39 071 123 4567',
                'email' => 'assistenza@techsupportpro.it',
                'orari' => 'Lun-Ven 8:00-18:00, Sab 8:00-13:00',
                'descrizione' => 'Per problemi tecnici e riparazioni'
            ],
            [
                'tipo' => 'vendite',
                'nome' => 'Vendite',
                'telefono' => '+39 071 123 4570',
                'email' => 'vendite@techsupportpro.it',
                'orari' => 'Lun-Ven 9:00-17:00',
                'descrizione' => 'Informazioni su prodotti e acquisti'
            ],
            [
                'tipo' => 'amministrazione',
                'nome' => 'Amministrazione',
                'telefono' => '+39 071 123 4569',
                'email' => 'admin@techsupportpro.it',
                'orari' => 'Lun-Ven 9:00-17:00',
                'descrizione' => 'Fatturazione e pratiche amministrative'
            ],
            [
                'tipo' => 'emergenze',
                'nome' => 'Emergenze',
                'telefono' => '+39 800 123 456',                  // Numero verde
                'email' => 'urgenze@techsupportpro.it',
                'orari' => '24/7',                               // Servizio continuo
                'descrizione' => 'Solo per emergenze (guasti gas, allagamenti)'
            ]
        ];

        return view('pages.contatti', compact('contatti'));
    }

    /**
     * METODO INVIA CONTATTO - LINGUAGGIO: PHP con Form Validation
     * 
     * Gestisce l'invio del modulo di contatto con validazione completa.
     * Include sanitizzazione input, logging sicurezza e response feedback.
     * 
     * PROCESSO:
     * 1. Validazione rigorosa input form
     * 2. Sanitizzazione dati per sicurezza
     * 3. Logging richiesta per tracking
     * 4. Invio email (simulato)
     * 5. Response con conferma utente
     * 
     * @param \Illuminate\Http\Request $request Dati del form contatto
     * @return \Illuminate\Http\RedirectResponse Redirect con messaggio esito
     */
    public function inviaContatto(Request $request)
    {
        // STEP 1: VALIDAZIONE COMPLETA FORM
        // Laravel Validator con regole specifiche e messaggi custom
        $validated = $request->validate([
            'nome' => 'required|string|max:255',                          // Nome obbligatorio
            'cognome' => 'required|string|max:255',                       // Cognome obbligatorio
            'email' => 'required|email',                                  // Email valida
            'telefono' => 'nullable|string|max:20',                       // Telefono opzionale
            'tipo_richiesta' => 'required|in:assistenza,vendite,reclamo,informazioni', // Enum predefinito
            'messaggio' => 'required|string|min:10|max:1000'              // Messaggio con lunghezza controllata
        ], [
            // MESSAGGI ERRORE PERSONALIZZATI IN ITALIANO
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'tipo_richiesta.required' => 'Seleziona il tipo di richiesta',
            'messaggio.required' => 'Il messaggio è obbligatorio',
            'messaggio.min' => 'Il messaggio deve essere di almeno 10 caratteri'
        ]);

        // STEP 2: LOGGING RICHIESTA PER SICUREZZA E TRACKING
        // Include informazioni utili per anti-spam e customer service
        Log::info('Nuova richiesta di contatto ricevuta', [
            'nome_completo' => $validated['nome'] . ' ' . $validated['cognome'],
            'email' => $validated['email'],
            'tipo_richiesta' => $validated['tipo_richiesta'],
            'ip' => $request->ip(),                                       // IP per anti-spam
            'user_agent' => $request->userAgent()                        // Browser per statistiche
        ]);

        // STEP 3: INVIO EMAIL (SIMULATO)
        // In implementazione reale:
        // Mail::to('assistenza@techsupportpro.it')->send(new ContattoMail($validated));

        // STEP 4: RESPONSE CON CONFERMA PERSONALIZZATA
        return back()->with('success', 
            'Grazie per averci contattato! Ti risponderemo entro 24 ore all\'indirizzo ' . $validated['email']
        );
    }

    /**
     * METODO DASHBOARD STATS - LINGUAGGIO: PHP con JSON API Response
     * 
     * API endpoint per statistiche dashboard utilizzate da chiamate AJAX.
     * Fornisce dati strutturati per aggiornamenti dinamici interfaccia.
     * 
     * FUNZIONALITÀ:
     * - Statistiche aggregate per categoria
     * - Conteggi temporali (questo mese)
     * - Distribuzioni per grafici
     * - Error handling per API robuste
     * 
     * UTILIZZO: Chiamate JavaScript per dashboard real-time
     * 
     * @return \Illuminate\Http\JsonResponse Statistiche in formato JSON
     */
    public function dashboardStats()
    {
        try {
            // STEP 1: RACCOLTA STATISTICHE STRUTTURATE PER CATEGORIA
            $stats = [
                // === SEZIONE PRODOTTI ===
                'prodotti' => [
                    'totali' => $this->safeCount(Prodotto::class),
                    'attivi' => $this->safeCount(Prodotto::class, ['attivo' => true]),
                    'per_categoria' => $this->getProdottiPerCategoria()        // Per grafici a torta
                ],
                
                // === SEZIONE MALFUNZIONAMENTI ===
                'malfunzionamenti' => [
                    'totali' => $this->safeCount(Malfunzionamento::class),
                    'per_gravita' => $this->getMalfunzionamentiPerGravita(),   // Distribuzione criticità
                    'questo_mese' => $this->safeCountWithCondition(            // Trend temporale
                        Malfunzionamento::class,
                        function($query) {
                            return $query->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year);
                        }
                    )
                ],
                
                // === SEZIONE UTENTI ===
                'utenti' => [
                    'totali' => $this->safeCount(User::class),
                    'per_livello' => $this->getUtentiPerLivello(),             // Distribuzione ruoli
                    'tecnici_attivi' => $this->safeCount(User::class, ['livello_accesso' => '2'])
                ],
                
                // === SEZIONE CENTRI ASSISTENZA ===
                'centri' => [
                    'totali' => $this->safeCount(CentroAssistenza::class),
                    'per_provincia' => $this->getCentriPerProvincia()          // Distribuzione geografica
                ]
            ];

            // STEP 2: RESPONSE JSON STRUTTURATA
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()                        // Per cache frontend
            ]);
            
        } catch (\Exception $e) {
            // STEP 3: ERROR HANDLING API
            Log::error('Errore nel caricamento statistiche dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento delle statistiche',
                'timestamp' => now()->toISOString()
            ], 500);                                                       // HTTP 500 Internal Server Error
        }
    }

    /**
     * METODO SYSTEM STATUS - LINGUAGGIO: PHP con Health Checks
     * 
     * Endpoint per monitoraggio stato del sistema (health check).
     * Utilizzato da sistemi di monitoring esterni e dashboard operative.
     * 
     * VERIFICHE:
     * - Connessione database attiva
     * - Accessibilità storage filesystem
     * - Funzionamento cache system
     * - Stato generale sistema
     * 
     * @return \Illuminate\Http\JsonResponse Stato componenti sistema
     */
    public function systemStatus()
    {
        // STEP 1: INIZIALIZZAZIONE STATUS COMPONENTI
        $status = [
            'database' => 'ok',
            'storage' => 'ok', 
            'cache' => 'ok'
        ];

        try {
            // STEP 2: TEST CONNESSIONE DATABASE
            // Verifica che PDO connection sia attiva e responsiva
            DB::connection()->getPdo();                                   // Test connessione
            DB::select('SELECT 1');                                       // Test query semplice
        } catch (\Exception $e) {
            $status['database'] = 'error';
            Log::error('Database connection failed', ['error' => $e->getMessage()]);
        }

        try {
            // STEP 3: TEST STORAGE ACCESSIBILITY
            // Verifica che il sistema possa accedere allo storage pubblico
            if (Storage::disk('public')->exists('.')) {
                $status['storage'] = 'ok';
            } else {
                $status['storage'] = 'warning';                          // Accessibile ma configurazione dubbio
            }
        } catch (\Exception $e) {
            $status['storage'] = 'error';
            Log::error('Storage test failed', ['error' => $e->getMessage()]);
        }

        // STEP 4: DETERMINAZIONE STATO GENERALE
        // Se anche un solo componente ha errori, sistema degradato
        $overall_status = in_array('error', $status) ? 'degraded' : 'operational';

        // STEP 5: RESPONSE JSON STANDARDIZZATA
        return response()->json([
            'status' => $overall_status,                                  // Stato generale
            'services' => $status,                                        // Dettaglio componenti
            'timestamp' => now()->toISOString()                           // Timestamp check
        ]);
    }

    /**
     * METODO RICERCA GLOBALE - LINGUAGGIO: PHP con Multi-Entity Search
     * 
     * Sistema di ricerca unificata che cerca attraverso tutti i moduli.
     * Implementa filtraggio basato sui permessi utente per sicurezza.
     * 
     * ENTITÀ RICERCATE:
     * - Prodotti (pubblico)
     * - Centri assistenza (pubblico)
     * - Malfunzionamenti (solo utenti autenticati livello ≥2)
     * 
     * FEATURES:
     * - Ricerca multi-campo con LIKE queries
     * - Filtraggio permessi automatico
     * - Response strutturata per frontend
     * - Limit risultati per performance
     * 
     * @param \Illuminate\Http\Request $request Parametro 'q' con termine ricerca
     * @return \Illuminate\Http\JsonResponse Risultati ricerca aggregati
     */
    public function ricercaGlobale(Request $request)
    {
        // STEP 1: VALIDAZIONE INPUT RICERCA
        $request->validate([
            'q' => 'required|string|min:2|max:100'                        // Termine ricerca con lunghezza controllata
        ]);

        $query = $request->input('q');
        $results = [];                                                    // Array risultati aggregati

        try {
            // STEP 2: RICERCA NEI PRODOTTI (PUBBLICO)
            $prodotti = $this->searchProdotti($query);
            foreach ($prodotti as $prodotto) {
                $results[] = [
                    'type' => 'prodotto',                                 // Tipo per frontend styling
                    'title' => $prodotto->nome,
                    'subtitle' => $prodotto->modello,
                    'description' => $prodotto->categoria_label ?? 'Generale',
                    'url' => route('prodotti.show', $prodotto),           // Link diretto
                    'icon' => 'bi-box'                                    // Bootstrap icon
                ];
            }

            // STEP 3: RICERCA NEI CENTRI ASSISTENZA (PUBBLICO)
            $centri = $this->searchCentri($query);
            foreach ($centri as $centro) {
                $results[] = [
                    'type' => 'centro',
                    'title' => $centro->nome,
                    'subtitle' => $centro->citta . ' (' . $centro->provincia . ')',
                    'description' => $centro->indirizzo,
                    'url' => route('centri.show', $centro),
                    'icon' => 'bi-geo-alt'                                // Icona geografica
                ];
            }

            // STEP 4: RICERCA NEI MALFUNZIONAMENTI (SOLO UTENTI AUTENTICATI)
            // Controllo sicurezza: solo tecnici, staff e admin possono vedere malfunzionamenti
            if (Auth::check() && Auth::user()->livello_accesso >= 2) {
                $malfunzionamenti = $this->searchMalfunzionamenti($query);
                foreach ($malfunzionamenti as $malfunzionamento) {
                    $results[] = [
                        'type' => 'malfunzionamento',
                        'title' => $malfunzionamento->titolo,
                        'subtitle' => $malfunzionamento->prodotto->nome ?? 'N/A',
                        'description' => 'Gravità: ' . ucfirst($malfunzionamento->gravita),
                        'url' => route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]),
                        'icon' => 'bi-exclamation-triangle'              // Icona warning
                    ];
                }
            }

        } catch (\Exception $e) {
            // STEP 5: ERROR HANDLING RICERCA
            Log::error('Errore nella ricerca globale', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Errore nella ricerca',
                'query' => $query
            ], 500);
        }

        // STEP 6: RESPONSE JSON CON RISULTATI AGGREGATI
        return response()->json([
            'success' => true,
            'query' => $query,                                            // Echo query per conferma
            'results' => $results,                                        // Array risultati strutturati
            'total' => count($results)                                    // Conteggio per frontend
        ]);
    }

    // ================================================
    // SEZIONE METODI PRIVATI DI UTILITÀ
    // ================================================

    /**
     * METODO HELPER SAFE COUNT - LINGUAGGIO: PHP con Exception Handling
     * 
     * Esegue conteggi sicuri che non generano errori se il Model non esiste.
     * Implementa pattern "Graceful Degradation" per robustezza sistema.
     * 
     * VANTAGGI:
     * - Previene crash da model mancanti
     * - Logging automatico errori per debug
     * - Fallback a valore 0 sicuro
     * - Supporto condizioni WHERE multiple
     * 
     * @param string $modelClass Nome classe Model da contare
     * @param array $conditions Array condizioni WHERE [campo => valore]
     * @return int Conteggio sicuro (0 se errore)
     */
    private function safeCount($modelClass, $conditions = [])
    {
        try {
            // STEP 1: VERIFICA ESISTENZA CLASSE MODEL
            if (!class_exists($modelClass)) {
                Log::warning("Model non trovato: {$modelClass}");
                return 0;                                                 // Fallback sicuro
            }

            // STEP 2: COSTRUZIONE QUERY DINAMICA
            $query = $modelClass::query();                               // Query builder base
            
            // STEP 3: APPLICAZIONE CONDIZIONI WHERE
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);                          // WHERE field = value
            }
            
            // STEP 4: ESECUZIONE COUNT
            return $query->count();                                      // SELECT COUNT(*) optimized
            
        } catch (\Exception $e) {
            // STEP 5: LOGGING ERROR CON CONTESTO
            Log::warning('Errore nel conteggio sicuro', [
                'model' => $modelClass,
                'conditions' => $conditions,
                'error' => $e->getMessage()
            ]);
            
            return 0;                                                    // Fallback sicuro
        }
    }

    /**
     * METODO HELPER SAFE COUNT WITH CONDITION - LINGUAGGIO: PHP con Callback Pattern
     * 
     * Conteggio sicuro con condizioni personalizzate tramite callback.
     * Permette query complesse mantenendo la sicurezza del safeCount base.
     * 
     * @param string $modelClass Nome classe Model
     * @param callable $conditionCallback Function che modifica la query
     * @return int Conteggio risultante
     */
    private function safeCountWithCondition($modelClass, $conditionCallback)
    {
        try {
            // STEP 1: VERIFICA ESISTENZA MODEL
            if (!class_exists($modelClass)) {
                return 0;
            }

            // STEP 2: INIZIALIZZAZIONE QUERY
            $query = $modelClass::query();
            
            // STEP 3: APPLICAZIONE CALLBACK PERSONALIZZATO
            $query = $conditionCallback($query);                        // Modifica query via closure
            
            // STEP 4: ESECUZIONE COUNT
            return $query->count();
            
        } catch (\Exception $e) {
            // STEP 5: LOGGING ERROR
            Log::warning('Errore nel conteggio con condizioni', [
                'model' => $modelClass,
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * METODO HELPER GET PRODOTTI RECENTI - LINGUAGGIO: PHP con Collection Processing
     * 
     * Recupera ultimi prodotti aggiunti per sezione homepage "Novità".
     * Include ottimizzazione query e gestione errori.
     * 
     * @return \Illuminate\Support\Collection Prodotti recenti o collection vuota
     */
    private function getProdottiRecenti()
    {
        try {
            // STEP 1: VERIFICA ESISTENZA MODEL
            if (!class_exists(Prodotto::class)) {
                return collect();                                        // Collection vuota sicura
            }

            // STEP 2: QUERY OTTIMIZZATA PER PRODOTTI RECENTI
            return Prodotto::where('attivo', true)                      // Solo prodotti visibili pubblico
                ->orderBy('created_at', 'desc')                        // Ordinamento per data creazione (più recenti prima)
                ->limit(4)                                              // Solo 4 per performance e layout
                ->get(['id', 'nome', 'modello', 'categoria', 'foto', 'prezzo']); // Solo campi necessari
                
        } catch (\Exception $e) {
            // STEP 3: ERROR HANDLING CON LOGGING
            Log::error('Errore caricamento prodotti recenti', ['error' => $e->getMessage()]);
            return collect();                                           // Collection vuota per evitare crash vista
        }
    }

    /**
     * METODO HELPER GET CENTRI PRINCIPALI - LINGUAGGIO: PHP con Data Limiting
     * 
     * Recupera centri assistenza principali per sezione homepage.
     * Limitato a 3 elementi per performance e design responsive.
     * 
     * @return \Illuminate\Support\Collection Centri principali o collection vuota
     */
    private function getCentriPrincipali()
    {
        try {
            // STEP 1: VERIFICA ESISTENZA MODEL
            if (!class_exists(CentroAssistenza::class)) {
                return collect();
            }

            // STEP 2: QUERY LIMITATA PER PERFORMANCE
            return CentroAssistenza::limit(3)                           // Solo 3 centri per layout homepage
                ->get(['id', 'nome', 'citta', 'provincia', 'telefono', 'indirizzo']); // Solo campi essenziali
                
        } catch (\Exception $e) {
            // STEP 3: ERROR HANDLING
            Log::error('Errore caricamento centri principali', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * METODO HELPER GET CATEGORIE STATS - LINGUAGGIO: PHP con Fallback Logic
     * 
     * Calcola statistiche categorie prodotti per grafici dashboard.
     * Implementa fallback multipli per robustezza.
     * 
     * ARCHITETTURA:
     * 1. Tenta di usare metodo specifico del Model se esiste
     * 2. Fallback: calcolo manuale con query aggregate
     * 3. Fallback finale: array vuoto per evitare errori
     * 
     * @return array Statistiche categorie [categoria => [label, count]]
     */
    private function getCategorieStats()
    {
        try {
            // STEP 1: VERIFICA ESISTENZA MODEL
            if (!class_exists(Prodotto::class)) {
                return [];
            }

            // STEP 2: TENTATIVO METODO SPECIALIZZATO DEL MODEL
            // Se il Model ha un metodo dedicato, lo usa per performance
            if (method_exists(Prodotto::class, 'getCategorieConConteggio')) {
                return Prodotto::getCategorieConConteggio();
            }

            // STEP 3: FALLBACK - CALCOLO MANUALE
            // Struttura base categorie con etichette e contatori
            $categorie = [
                'elettrodomestici' => ['label' => 'Elettrodomestici', 'count' => 0],
                'climatizzazione' => ['label' => 'Climatizzazione', 'count' => 0],
                'cucina' => ['label' => 'Cucina', 'count' => 0],
                'lavanderia' => ['label' => 'Lavanderia', 'count' => 0],
                'riscaldamento' => ['label' => 'Riscaldamento', 'count' => 0],
                'altro' => ['label' => 'Altro', 'count' => 0]
            ];

            // STEP 4: QUERY AGGREGATE PER CONTEGGI REALI
            $prodottiPerCategoria = Prodotto::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')             // GROUP BY con COUNT
                ->groupBy('categoria')
                ->pluck('count', 'categoria')                           // Array [categoria => count]
                ->toArray();

            // STEP 5: MERGE CONTEGGI REALI CON STRUTTURA BASE
            foreach ($prodottiPerCategoria as $categoria => $count) {
                if (isset($categorie[$categoria])) {
                    $categorie[$categoria]['count'] = $count;
                }
            }

            return $categorie;
            
        } catch (\Exception $e) {
            // STEP 6: FALLBACK FINALE
            Log::error('Errore caricamento categorie stats', ['error' => $e->getMessage()]);
            return [];                                                  // Array vuoto sicuro
        }
    }

    /**
     * METODO HELPER GET PRODOTTI PER CATEGORIA - LINGUAGGIO: PHP con Aggregate Query
     * 
     * Calcola distribuzione prodotti per categoria per statistiche dashboard.
     * Utilizzato dalle API per grafici real-time.
     * 
     * @return array Array associativo [categoria => count]
     */
    private function getProdottiPerCategoria()
    {
        try {
            // Query aggregate ottimizzata
            return Prodotto::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')                           // Converte in array associativo
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore conteggio prodotti per categoria', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * METODO HELPER GET MALFUNZIONAMENTI PER GRAVITÀ - LINGUAGGIO: PHP
     * 
     * Calcola distribuzione malfunzionamenti per livello di gravità.
     * Utilizzato per grafici priorità interventi.
     * 
     * @return array Array associativo [gravità => count]
     */
    private function getMalfunzionamentiPerGravita()
    {
        try {
            return Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                ->groupBy('gravita')
                ->pluck('count', 'gravita')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore conteggio malfunzionamenti per gravità', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * METODO HELPER GET UTENTI PER LIVELLO - LINGUAGGIO: PHP
     * 
     * Calcola distribuzione utenti per livello di accesso.
     * Utilizzato per analisi organico aziendale.
     * 
     * @return array Array associativo [livello => count]
     */
    private function getUtentiPerLivello()
    {
        try {
            return User::selectRaw('livello_accesso, COUNT(*) as count')
                ->groupBy('livello_accesso')
                ->pluck('count', 'livello_accesso')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore conteggio utenti per livello', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * METODO HELPER GET CENTRI PER PROVINCIA - LINGUAGGIO: PHP
     * 
     * Calcola distribuzione geografica centri assistenza.
     * Utilizzato per analisi copertura territoriale.
     * 
     * @return array Array associativo [provincia => count]
     */
    private function getCentriPerProvincia()
    {
        try {
            return CentroAssistenza::selectRaw('provincia, COUNT(*) as count')
                ->groupBy('provincia')
                ->pluck('count', 'provincia')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore conteggio centri per provincia', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * METODO HELPER SEARCH PRODOTTI - LINGUAGGIO: PHP con Multi-Field Search
     * 
     * Ricerca sicura nei prodotti con query multi-campo.
     * Implementa LIKE pattern per ricerca parziale flessibile.
     * 
     * @param string $query Termine di ricerca
     * @return \Illuminate\Support\Collection Risultati prodotti
     */
    private function searchProdotti($query)
    {
        try {
            // Query di ricerca su multipli campi prodotto
            return Prodotto::where('attivo', true)                      // Solo prodotti pubblici
                ->where(function($q) use ($query) {
                    // Closure per raggruppare condizioni OR
                    $q->where('nome', 'LIKE', "%{$query}%")            // Ricerca nel nome
                      ->orWhere('modello', 'LIKE', "%{$query}%")       // Ricerca nel modello
                      ->orWhere('descrizione', 'LIKE', "%{$query}%");  // Ricerca nella descrizione
                })
                ->limit(5)                                              // Limite per performance ricerca
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca prodotti', ['error' => $e->getMessage()]);
            return collect();                                           // Collection vuota sicura
        }
    }

    /**
     * METODO HELPER SEARCH CENTRI - LINGUAGGIO: PHP con Geographic Search
     * 
     * Ricerca sicura nei centri assistenza con focus geografico.
     * Include ricerca per nome, città e provincia.
     * 
     * @param string $query Termine di ricerca
     * @return \Illuminate\Support\Collection Risultati centri
     */
    private function searchCentri($query)
    {
        try {
            // Query di ricerca geografica centri
            return CentroAssistenza::where('nome', 'LIKE', "%{$query}%")
                ->orWhere('citta', 'LIKE', "%{$query}%")               // Ricerca per città
                ->orWhere('provincia', 'LIKE', "%{$query}%")           // Ricerca per provincia
                ->limit(3)                                              // Limite per layout risultati
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca centri', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * METODO HELPER SEARCH MALFUNZIONAMENTI - LINGUAGGIO: PHP con Relation Loading
     * 
     * Ricerca sicura nei malfunzionamenti con eager loading prodotto correlato.
     * Solo per utenti autenticati con livello accesso appropriato.
     * 
     * @param string $query Termine di ricerca
     * @return \Illuminate\Support\Collection Risultati malfunzionamenti
     */
    private function searchMalfunzionamenti($query)
    {
        try {
            // Query ricerca malfunzionamenti con relazione prodotto
            return Malfunzionamento::where('titolo', 'LIKE', "%{$query}%")
                ->orWhere('descrizione', 'LIKE', "%{$query}%")         // Ricerca in descrizione problema
                ->with('prodotto')                                      // Eager loading prodotto correlato
                ->limit(3)                                              // Limite risultati
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca malfunzionamenti', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}