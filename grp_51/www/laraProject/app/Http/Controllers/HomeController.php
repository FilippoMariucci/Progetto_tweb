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
 * Controller per la gestione della homepage e pagine informative
 * Gestisce statistiche, informazioni azienda e funzionalità pubbliche
 */
class HomeController extends Controller
{
    /**
     * Mostra la homepage con statistiche e informazioni generali
     * Accessibile a tutti (livello 1 - pubblico)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            Log::info('Caricamento homepage iniziato');

            // === RACCOLTA STATISTICHE CON FALLBACK SICURI ===
            $stats = [
                // Conteggio prodotti attivi nel catalogo
                'prodotti_totali' => $this->safeCount(Prodotto::class, ['attivo' => true]),
                
                // Conteggio centri di assistenza
                'centri_totali' => $this->safeCount(CentroAssistenza::class),
                
                // Conteggio soluzioni disponibili (malfunzionamenti con soluzione)
                'soluzioni_totali' => $this->safeCountWithCondition(
                    Malfunzionamento::class, 
                    function($query) {
                        return $query->whereNotNull('soluzione');
                    }
                ),
                
                // Conteggio tecnici specializzati (livello accesso 2)
                'tecnici_totali' => $this->safeCount(User::class, ['livello_accesso' => '2']),
                
                // Conteggio anni di esperienza
                'anni_esperienza' => date('Y') - 1994,
            ];

            Log::info('Statistiche calcolate', $stats);

            // === DATI AGGIUNTIVI PER LA VISTA ===
            
            // Ultimi prodotti aggiunti (per showcase)
            $prodotti_recenti = $this->getProdottiRecenti();

            // Centri assistenza principali (per esempio)
            $centri_principali = $this->getCentriPrincipali();

            // Categorie con conteggio prodotti
            $categorie_stats = $this->getCategorieStats();

            Log::info('Homepage caricata con successo', [
                'stats' => $stats,
                'prodotti_recenti_count' => $prodotti_recenti->count(),
                'centri_principali_count' => $centri_principali->count(),
                'categorie_count' => count($categorie_stats)
            ]);

        } catch (\Exception $e) {
            // Fallback in caso di errore - usa valori predefiniti
            Log::error('Errore nel caricamento homepage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $stats = [
                'prodotti_totali' => 150,
                'centri_totali' => 25, 
                'soluzioni_totali' => 500,
                'tecnici_totali' => 50,
                'anni_esperienza' => 30
            ];
            
            $prodotti_recenti = collect();
            $centri_principali = collect();
            $categorie_stats = [];
        }

        return view('home', compact(
            'stats', 
            'prodotti_recenti', 
            'centri_principali', 
            'categorie_stats'
        ));
    }

    /**
     * Pagina "Chi Siamo" con informazioni aziendali
     * 
     * @return \Illuminate\View\View
     */
    public function azienda()
    {
        // Informazioni dettagliate sull'azienda
        $azienda_info = [
            'nome' => 'TechSupport Pro S.r.l.',
            'founded' => '1994',
            'sede_legale' => [
                'indirizzo' => 'Via dell\'Industria, 123',
                'citta' => 'Ancona',
                'cap' => '60121',
                'provincia' => 'AN',
                'regione' => 'Marche'
            ],
            'contatti' => [
                'telefono_principale' => '+39 071 123 4567',
                'email_generale' => 'info@techsupportpro.it',
                'email_assistenza' => 'assistenza@techsupportpro.it',
                'fax' => '+39 071 123 4568'
            ],
            'certificazioni' => [
                'iso_9001' => 'Gestione Qualità',
                'iso_14001' => 'Gestione Ambientale',
                'ce_marking' => 'Conformità Europea',
                'energy_star' => 'Efficienza Energetica'
            ],
            'numeri' => [
                'anni_esperienza' => date('Y') - 1994,
                'dipendenti' => 850,
                'centri_assistenza' => $this->safeCount(CentroAssistenza::class),
                'prodotti_catalogo' => $this->safeCount(Prodotto::class, ['attivo' => true]),
                'clienti_serviti' => '2.5M+',
                'interventi_anno' => '150K+'
            ]
        ];

        // Timeline aziendale
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

        return view('pages.azienda', compact('azienda_info', 'timeline'));
    }

    /**
     * Pagina contatti con informazioni di supporto
     * 
     * @return \Illuminate\View\View
     */
    public function contatti()
    {
        // Informazioni di contatto organizzate per tipo
        $contatti = [
            [
                'tipo' => 'assistenza_tecnica',
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
                'telefono' => '+39 800 123 456',
                'email' => 'urgenze@techsupportpro.it',
                'orari' => '24/7',
                'descrizione' => 'Solo per emergenze (guasti gas, allagamenti)'
            ]
        ];

        return view('pages.contatti', compact('contatti'));
    }

    /**
     * Gestisce l'invio del modulo di contatto
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviaContatto(Request $request)
    {
        // Validazione del modulo
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'nullable|string|max:20',
            'tipo_richiesta' => 'required|in:assistenza,vendite,reclamo,informazioni',
            'messaggio' => 'required|string|min:10|max:1000'
        ], [
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Inserisci un\'email valida',
            'tipo_richiesta.required' => 'Seleziona il tipo di richiesta',
            'messaggio.required' => 'Il messaggio è obbligatorio',
            'messaggio.min' => 'Il messaggio deve essere di almeno 10 caratteri'
        ]);

        // Log della richiesta di contatto
        Log::info('Nuova richiesta di contatto ricevuta', [
            'nome_completo' => $validated['nome'] . ' ' . $validated['cognome'],
            'email' => $validated['email'],
            'tipo_richiesta' => $validated['tipo_richiesta'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // In un'implementazione reale, qui invieresti l'email
        // Mail::to('assistenza@techsupportpro.it')->send(new ContattoMail($validated));

        return back()->with('success', 
            'Grazie per averci contattato! Ti risponderemo entro 24 ore all\'indirizzo ' . $validated['email']
        );
    }

    /**
     * API per statistiche dashboard (per chiamate AJAX)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardStats()
    {
        try {
            $stats = [
                'prodotti' => [
                    'totali' => $this->safeCount(Prodotto::class),
                    'attivi' => $this->safeCount(Prodotto::class, ['attivo' => true]),
                    'per_categoria' => $this->getProdottiPerCategoria()
                ],
                'malfunzionamenti' => [
                    'totali' => $this->safeCount(Malfunzionamento::class),
                    'per_gravita' => $this->getMalfunzionamentiPerGravita(),
                    'questo_mese' => $this->safeCountWithCondition(
                        Malfunzionamento::class,
                        function($query) {
                            return $query->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year);
                        }
                    )
                ],
                'utenti' => [
                    'totali' => $this->safeCount(User::class),
                    'per_livello' => $this->getUtentiPerLivello(),
                    'tecnici_attivi' => $this->safeCount(User::class, ['livello_accesso' => '2'])
                ],
                'centri' => [
                    'totali' => $this->safeCount(CentroAssistenza::class),
                    'per_provincia' => $this->getCentriPerProvincia()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Errore nel caricamento statistiche dashboard', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Errore nel caricamento delle statistiche',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Endpoint per verificare lo stato del sistema
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function systemStatus()
    {
        $status = [
            'database' => 'ok',
            'storage' => 'ok',
            'cache' => 'ok'
        ];

        try {
            // Test connessione database
            DB::connection()->getPdo();
            DB::select('SELECT 1');
        } catch (\Exception $e) {
            $status['database'] = 'error';
            Log::error('Database connection failed', ['error' => $e->getMessage()]);
        }

        try {
            // Test storage
            if (Storage::disk('public')->exists('.')) {
                $status['storage'] = 'ok';
            } else {
                $status['storage'] = 'warning';
            }
        } catch (\Exception $e) {
            $status['storage'] = 'error';
            Log::error('Storage test failed', ['error' => $e->getMessage()]);
        }

        $overall_status = in_array('error', $status) ? 'degraded' : 'operational';

        return response()->json([
            'status' => $overall_status,
            'services' => $status,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Ricerca globale nel sistema
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ricercaGlobale(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $query = $request->input('q');
        $results = [];

        try {
            // Ricerca nei prodotti
            $prodotti = $this->searchProdotti($query);
            foreach ($prodotti as $prodotto) {
                $results[] = [
                    'type' => 'prodotto',
                    'title' => $prodotto->nome,
                    'subtitle' => $prodotto->modello,
                    'description' => $prodotto->categoria_label ?? 'Generale',
                    'url' => route('prodotti.show', $prodotto),
                    'icon' => 'bi-box'
                ];
            }

            // Ricerca nei centri assistenza
            $centri = $this->searchCentri($query);
            foreach ($centri as $centro) {
                $results[] = [
                    'type' => 'centro',
                    'title' => $centro->nome,
                    'subtitle' => $centro->citta . ' (' . $centro->provincia . ')',
                    'description' => $centro->indirizzo,
                    'url' => route('centri.show', $centro),
                    'icon' => 'bi-geo-alt'
                ];
            }

            // Ricerca nei malfunzionamenti (solo per utenti autenticati)
            if (Auth::check() && Auth::user()->livello_accesso >= 2) {
                $malfunzionamenti = $this->searchMalfunzionamenti($query);
                foreach ($malfunzionamenti as $malfunzionamento) {
                    $results[] = [
                        'type' => 'malfunzionamento',
                        'title' => $malfunzionamento->titolo,
                        'subtitle' => $malfunzionamento->prodotto->nome ?? 'N/A',
                        'description' => 'Gravità: ' . ucfirst($malfunzionamento->gravita),
                        'url' => route('malfunzionamenti.show', [$malfunzionamento->prodotto, $malfunzionamento]),
                        'icon' => 'bi-exclamation-triangle'
                    ];
                }
            }

        } catch (\Exception $e) {
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

        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'total' => count($results)
        ]);
    }

    // ================================================
    // METODI PRIVATI DI UTILITÀ
    // ================================================

    /**
     * Conteggio sicuro che non genera errori se il Model non esiste
     * 
     * @param string $modelClass
     * @param array $conditions
     * @return int
     */
    private function safeCount($modelClass, $conditions = [])
    {
        try {
            if (!class_exists($modelClass)) {
                Log::warning("Model non trovato: {$modelClass}");
                return 0;
            }

            $query = $modelClass::query();
            
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            
            return $query->count();
            
        } catch (\Exception $e) {
            Log::warning('Errore nel conteggio sicuro', [
                'model' => $modelClass,
                'conditions' => $conditions,
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Conteggio sicuro con condizioni personalizzate
     * 
     * @param string $modelClass
     * @param callable $conditionCallback
     * @return int
     */
    private function safeCountWithCondition($modelClass, $conditionCallback)
    {
        try {
            if (!class_exists($modelClass)) {
                return 0;
            }

            $query = $modelClass::query();
            $query = $conditionCallback($query);
            
            return $query->count();
            
        } catch (\Exception $e) {
            Log::warning('Errore nel conteggio con condizioni', [
                'model' => $modelClass,
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Ottiene i prodotti recenti in modo sicuro
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getProdottiRecenti()
    {
        try {
            if (!class_exists(Prodotto::class)) {
                return collect();
            }

            return Prodotto::where('attivo', true)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get(['id', 'nome', 'modello', 'categoria', 'foto', 'prezzo']);
                
        } catch (\Exception $e) {
            Log::error('Errore caricamento prodotti recenti', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ottiene i centri principali in modo sicuro
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getCentriPrincipali()
    {
        try {
            if (!class_exists(CentroAssistenza::class)) {
                return collect();
            }

            return CentroAssistenza::limit(3)
                ->get(['id', 'nome', 'citta', 'provincia', 'telefono', 'indirizzo']);
                
        } catch (\Exception $e) {
            Log::error('Errore caricamento centri principali', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ottiene le statistiche delle categorie
     * 
     * @return array
     */
    private function getCategorieStats()
    {
        try {
            if (!class_exists(Prodotto::class)) {
                return [];
            }

            // Prova prima a usare il metodo del model se esiste
            if (method_exists(Prodotto::class, 'getCategorieConConteggio')) {
                return Prodotto::getCategorieConConteggio();
            }

            // Fallback: calcola manualmente
            $categorie = [
                'elettrodomestici' => ['label' => 'Elettrodomestici', 'count' => 0],
                'climatizzazione' => ['label' => 'Climatizzazione', 'count' => 0],
                'cucina' => ['label' => 'Cucina', 'count' => 0],
                'lavanderia' => ['label' => 'Lavanderia', 'count' => 0],
                'riscaldamento' => ['label' => 'Riscaldamento', 'count' => 0],
                'altro' => ['label' => 'Altro', 'count' => 0]
            ];

            $prodottiPerCategoria = Prodotto::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray();

            foreach ($prodottiPerCategoria as $categoria => $count) {
                if (isset($categorie[$categoria])) {
                    $categorie[$categoria]['count'] = $count;
                }
            }

            return $categorie;
            
        } catch (\Exception $e) {
            Log::error('Errore caricamento categorie stats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Ottiene prodotti per categoria per le statistiche
     * 
     * @return array
     */
    private function getProdottiPerCategoria()
    {
        try {
            return Prodotto::where('attivo', true)
                ->selectRaw('categoria, COUNT(*) as count')
                ->groupBy('categoria')
                ->pluck('count', 'categoria')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Errore conteggio prodotti per categoria', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Ottiene malfunzionamenti per gravità
     * 
     * @return array
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
     * Ottiene utenti per livello
     * 
     * @return array
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
     * Ottiene centri per provincia
     * 
     * @return array
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
     * Ricerca sicura nei prodotti
     * 
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    private function searchProdotti($query)
    {
        try {
            return Prodotto::where('attivo', true)
                ->where(function($q) use ($query) {
                    $q->where('nome', 'LIKE', "%{$query}%")
                      ->orWhere('modello', 'LIKE', "%{$query}%")
                      ->orWhere('descrizione', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca prodotti', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ricerca sicura nei centri
     * 
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    private function searchCentri($query)
    {
        try {
            return CentroAssistenza::where('nome', 'LIKE', "%{$query}%")
                ->orWhere('citta', 'LIKE', "%{$query}%")
                ->orWhere('provincia', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca centri', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Ricerca sicura nei malfunzionamenti
     * 
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    private function searchMalfunzionamenti($query)
    {
        try {
            return Malfunzionamento::where('titolo', 'LIKE', "%{$query}%")
                ->orWhere('descrizione', 'LIKE', "%{$query}%")
                ->with('prodotto')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            Log::error('Errore ricerca malfunzionamenti', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}