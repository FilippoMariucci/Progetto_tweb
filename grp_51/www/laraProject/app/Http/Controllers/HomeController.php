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

class HomeController extends Controller
{
    /**
     * Mostra la homepage con statistiche e informazioni generali
     * Accessibile a tutti (livello 1 - pubblico)
     */
    public function index()
    {
        try {
            // === RACCOLTA STATISTICHE PER LA HOMEPAGE ===
            $stats = [
                // Conteggio prodotti attivi nel catalogo
                'prodotti_totali' => Prodotto::where('attivo', true)->count(),
                
                // Conteggio centri di assistenza
                'centri_totali' => CentroAssistenza::count(),
                
                // Conteggio soluzioni disponibili (malfunzionamenti con soluzione)
                'soluzioni_totali' => Malfunzionamento::whereNotNull('soluzione')->count(),
                
                // Conteggio tecnici specializzati
                'tecnici_totali' => User::where('livello_accesso', '2')->count(),
                
                // Conteggio anni di esperienza
                'anni_esperienza' => date('Y') - 1994,
            ];

            // === DATI AGGIUNTIVI PER LA VISTA ===
            
            // Ultimi prodotti aggiunti (per showcase)
            $prodotti_recenti = Prodotto::where('attivo', true)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get(['id', 'nome', 'modello', 'categoria', 'foto', 'prezzo']);

            // Centri assistenza principali (per esempio)
            $centri_principali = CentroAssistenza::limit(3)
                ->get(['id', 'nome', 'citta', 'provincia', 'telefono']);

            // Categorie con conteggio prodotti usando il metodo del Model
            $categorie_stats = Prodotto::getCategorieConConteggio();

        } catch (\Exception $e) {
            // Fallback in caso di errore
            Log::error('Errore nel caricamento homepage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $stats = [
                'prodotti_totali' => 0,
                'centri_totali' => 0, 
                'soluzioni_totali' => 0,
                'tecnici_totali' => 0,
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
     */
    public function dashboardStats()
    {
        try {
            $stats = [
                'prodotti' => [
                    'totali' => Prodotto::count(),
                    'attivi' => Prodotto::where('attivo', true)->count(),
                    'per_categoria' => Prodotto::selectRaw('categoria, COUNT(*) as count')
                        ->where('attivo', true)
                        ->groupBy('categoria')
                        ->pluck('count', 'categoria')
                ],
                'malfunzionamenti' => [
                    'totali' => Malfunzionamento::count(),
                    'per_gravita' => Malfunzionamento::selectRaw('gravita, COUNT(*) as count')
                        ->groupBy('gravita')
                        ->pluck('count', 'gravita'),
                    'questo_mese' => Malfunzionamento::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count()
                ],
                'utenti' => [
                    'totali' => User::count(),
                    'per_livello' => User::selectRaw('livello_accesso, COUNT(*) as count')
                        ->groupBy('livello_accesso')
                        ->pluck('count', 'livello_accesso'),
                    'tecnici_attivi' => User::where('livello_accesso', '2')->count()
                ],
                'centri' => [
                    'totali' => CentroAssistenza::count(),
                    'per_provincia' => CentroAssistenza::selectRaw('provincia, COUNT(*) as count')
                        ->groupBy('provincia')
                        ->pluck('count', 'provincia')
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
        } catch (\Exception $e) {
            $status['database'] = 'error';
            Log::error('Database connection failed', ['error' => $e->getMessage()]);
        }

        try {
            // Test storage
            Storage::disk('public')->exists('.');
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
            $prodotti = Prodotto::where('attivo', true)
                ->where(function($q) use ($query) {
                    $q->where('nome', 'LIKE', "%{$query}%")
                      ->orWhere('modello', 'LIKE', "%{$query}%")
                      ->orWhere('descrizione', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get();

            foreach ($prodotti as $prodotto) {
                $results[] = [
                    'type' => 'prodotto',
                    'title' => $prodotto->nome,
                    'subtitle' => $prodotto->modello,
                    'description' => $prodotto->categoria_label,
                    'url' => route('prodotti.show', $prodotto),
                    'icon' => 'bi-box'
                ];
            }

            // Ricerca nei centri assistenza
            $centri = CentroAssistenza::where('nome', 'LIKE', "%{$query}%")
                ->orWhere('citta', 'LIKE', "%{$query}%")
                ->orWhere('provincia', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();

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

            if (Auth::check() && Auth::user()->canViewMalfunzionamenti()) {
                $malfunzionamenti = Malfunzionamento::where('titolo', 'LIKE', "%{$query}%")
                    ->orWhere('descrizione', 'LIKE', "%{$query}%")
                    ->with('prodotto')
                    ->limit(3)
                    ->get();

                foreach ($malfunzionamenti as $malfunzionamento) {
                    $results[] = [
                        'type' => 'malfunzionamento',
                        'title' => $malfunzionamento->titolo,
                        'subtitle' => $malfunzionamento->prodotto->nome,
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
     */
    private function safeCount($modelClass, $conditions = [])
    {
        try {
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
}