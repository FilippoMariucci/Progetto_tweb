<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;
use Illuminate\Support\Facades\Auth;

/**
 * Controller per la gestione delle funzionalità riservate allo staff aziendale (Livello 3)
 * Lo staff può gestire malfunzionamenti e soluzioni dei prodotti
 */
class StaffController extends Controller
{
    /**
     * Costruttore del controller
     * Applica il middleware di autenticazione per verificare che l'utente sia loggato
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostra la dashboard dello staff con i prodotti disponibili
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Recupera tutti i prodotti dal database per mostrarli nella dashboard
        $prodotti = Prodotto::all();

        // Restituisce la vista della dashboard dello staff passando i prodotti
        return view('staff.dashboard', compact('prodotti'));
    }

    /**
     * Mostra i malfunzionamenti di un prodotto specifico
     * 
     * @param int $productId - ID del prodotto
     * @return \Illuminate\View\View
     */
    public function showMalfunzionamento($productId)
    {
        // Trova il prodotto specificato, altrimenti genera errore 404
        $prodotto = Prodotto::findOrFail($productId);
        
        // Recupera tutti i malfunzionamenti associati al prodotto
        $malfunzionamenti = $prodotto->malfunzionamenti;
        
        // Restituisce la vista con il prodotto e i suoi malfunzionamenti
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti'));
    }

    /**
     * Mostra il form per creare un nuovo malfunzionamento
     * 
     * @param int $productId - ID del prodotto a cui aggiungere il malfunzionamento
     * @return \Illuminate\View\View
     */
    public function createMalfunzionamento($productId)
    {
        // Trova il prodotto specificato
        $prodotto = Prodotto::findOrFail($productId);
        
        // Restituisce la vista del form di creazione
        return view('staff.create_malfunzionamento', compact('prodotto'));
    }

    /**
     * Salva un nuovo malfunzionamento nel database
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con i dati del form
     * @param int $productId - ID del prodotto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMalfunction(Request $request, $productId)
    {
        // Validazione dei dati di input
        $request->validate([
            'title' => 'required|string|max:255',        // Titolo obbligatorio, stringa, max 255 caratteri
            'description' => 'required|string',          // Descrizione obbligatoria
            'solution' => 'required|string',             // Soluzione obbligatoria
        ]);

        // Crea un nuovo malfunzionamento nel database
        Malfunzionamento::create([
            'prodotto_id' => $productId,                  // Associa il malfunzionamento al prodotto
            'title' => $request->title,                  // Titolo del malfunzionamento
            'description' => $request->description,      // Descrizione del problema
            'solution' => $request->solution,            // Soluzione tecnica
        ]);

        // Reindirizza alla pagina dei malfunzionamenti del prodotto con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento aggiunto con successo!');
    }

    /**
     * Mostra il form per modificare un malfunzionamento esistente
     * 
     * @param int $id - ID del malfunzionamento da modificare
     * @return \Illuminate\View\View
     */
    public function editMalfunction($id)
    {
        // Trova il malfunzionamento specificato con il prodotto associato
        $malfunzionamento = Malfunzionamento::with('prodotto')->findOrFail($id);
        
        // Restituisce la vista del form di modifica
        return view('staff.edit_malfunzionamento', compact('malfunzionamento'));
    }

    /**
     * Aggiorna un malfunzionamento esistente nel database
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con i nuovi dati
     * @param int $id - ID del malfunzionamento da aggiornare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMalfunction(Request $request, $id)
    {
        // Validazione dei dati di input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'solution' => 'required|string',
        ]);

        // Trova il malfunzionamento da aggiornare
        $malfunzionamento = Malfunzionamento::findOrFail($id);
        
        // Aggiorna i campi con i nuovi valori
        $malfunzionamento->update([
            'title' => $request->title,
            'description' => $request->description,
            'solution' => $request->solution,
        ]);

        // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $malfunzionamento->prodotto_id)
                        ->with('success', 'Malfunzionamento aggiornato con successo!');
    }

    /**
     * Elimina un malfunzionamento dal database
     * 
     * @param int $id - ID del malfunzionamento da eliminare
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMalfunction($id)
    {
        // Trova il malfunzionamento da eliminare
        $malfunzionamento = Malfunzionamento::findOrFail($id);
       $productId = $malfunzionamento->prodotto_id;  // Salva l'ID del prodotto per il redirect

        // Elimina il malfunzionamento dal database
        $malfunzionamento->delete();

        // Reindirizza alla pagina dei malfunzionamenti con messaggio di successo
        return redirect()->route('staff.malfunzionamenti', $productId)
                        ->with('success', 'Malfunzionamento eliminato con successo!');
    }

    /**
     * Ricerca malfunzionamenti per un prodotto specifico basandosi su un termine di ricerca
     * 
     * @param \Illuminate\Http\Request $request - Richiesta HTTP con il termine di ricerca
     * @param int $productId - ID del prodotto
     * @return \Illuminate\View\View
     */
    public function searchMalfunctions(Request $request, $productId)
    {
        // Trova il prodotto specificato
        $prodotto = Prodotto::findOrFail($productId);
        
        // Ottiene il termine di ricerca dalla richiesta
        $searchTerm = $request->get('search', '');
        
        // Se c'è un termine di ricerca, filtra i malfunzionamenti
        if ($searchTerm) {
            // Cerca nei malfunzionamenti del prodotto quelli che contengono il termine nella descrizione
            $malfunzionamenti = $prodotto->malfunzionamenti()
                                   ->where('description', 'like', '%' . $searchTerm . '%')
                                   ->get();
        } else {
            // Se non c'è termine di ricerca, mostra tutti i malfunzionamenti
            $malfunzionamenti = $prodotto->malfunzionamenti;
        }
        
        // Restituisce la vista con i risultati della ricerca
        return view('staff.malfunzionamenti', compact('prodotto', 'malfunzionamenti', 'searchTerm'));
    }
}