<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CentroAssistenza;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;

class DatabaseSeeder extends Seeder
{
    /**
     * Popola il database con dati di test
     * Crea gli utenti richiesti e dati di esempio per il sistema di assistenza tecnica
     */
    public function run(): void
    {
        // Disabilita temporaneamente i foreign key constraints per evitare errori di inserimento
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Pulisce le tabelle esistenti per evitare duplicati
        DB::table('malfunzionamenti')->delete();
        DB::table('prodotti')->delete();
        DB::table('centri_assistenza')->delete();
        DB::table('users')->delete();
        
        // Riabilita i foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Informazioni di debug
        
        $this->command->info('Database seeded successfully!');

        // Password derivata da SSH: dNWR53F3 -> primi 4 caratteri ripetuti: dNWRdNWR
        $password = Hash::make('dNWRdNWR');

        // === CREAZIONE CENTRI ASSISTENZA ===
        echo "Creazione centri assistenza...\n";
        $centri = [
            [
                'nome' => 'Centro Assistenza Nord',
                'indirizzo' => 'Via Milano 15',
                'citta' => 'Ancona',
                'provincia' => 'AN',
                'cap' => '60121',
                'telefono' => '071123456',
                'email' => 'nord@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Sud',
                'indirizzo' => 'Via Roma 32',
                'citta' => 'Pescara',
                'provincia' => 'PE', 
                'cap' => '65121',
                'telefono' => '085987654',
                'email' => 'sud@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Centro',
                'indirizzo' => 'Piazza Garibaldi 8',
                'citta' => 'Perugia',
                'provincia' => 'PG',
                'cap' => '06121',
                'telefono' => '075456789',
                'email' => 'centro@assistenza.it'
            ]
        ];

        foreach ($centri as $centro) {
            CentroAssistenza::create($centro);
        }

        // === CREAZIONE UTENTI RICHIESTI ===
        echo "Creazione utenti richiesti dalle specifiche...\n";

        // 1. Amministratore (livello 4) - username richiesto: adminadmin
        User::create([
            'username' => 'adminadmin',
            'password' => $password,
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'livello_accesso' => '4'
        ]);

        // 2. Staff Aziendale (livello 3) - username richiesto: staffstaff
        User::create([
            'username' => 'staffstaff',
            'password' => $password,
            'nome' => 'Giulia',
            'cognome' => 'Bianchi',
            'livello_accesso' => '3'
        ]);

        // 3. Tecnico (livello 2) - username richiesto: tecntecn
        User::create([
            'username' => 'tecntecn',
            'password' => $password,
            'nome' => 'Luca',
            'cognome' => 'Verdi',
            'livello_accesso' => '2',
            'data_nascita' => '1985-06-15',
            'specializzazione' => 'Elettrodomestici da cucina',
            'centro_assistenza_id' => 1 // Assegnato al primo centro
        ]);

        // === UTENTI AGGIUNTIVI PER TEST ===
        echo "Creazione utenti aggiuntivi per test...\n";

        // Staff aggiuntivo per test funzionalità opzionale
        User::create([
            'username' => 'staff2',
            'password' => $password,
            'nome' => 'Anna',
            'cognome' => 'Neri',
            'livello_accesso' => '3'
        ]);

        // Tecnici aggiuntivi per diversi centri
        User::create([
            'username' => 'tecnico2',
            'password' => $password,
            'nome' => 'Paolo',
            'cognome' => 'Gialli',
            'livello_accesso' => '2',
            'data_nascita' => '1990-03-20',
            'specializzazione' => 'Lavatrici e asciugatrici',
            'centro_assistenza_id' => 2
        ]);

        User::create([
            'username' => 'tecnico3',
            'password' => $password,
            'nome' => 'Sara',
            'cognome' => 'Blu',
            'livello_accesso' => '2',
            'data_nascita' => '1988-12-10',
            'specializzazione' => 'Frigoriferi e congelatori',
            'centro_assistenza_id' => 3
        ]);

        // === CREAZIONE PRODOTTI DI ESEMPIO ===
        echo "Creazione catalogo prodotti...\n";
        $prodotti = [
            [
                'nome' => 'Lavatrice EcoWash Pro',
                'modello' => 'LW-2024-001',
                'descrizione' => 'Lavatrice a carica frontale con tecnologia eco-friendly, capacità 8kg',
                'categoria' => 'lavatrice',
                'note_tecniche' => 'Classe energetica A+++, 1400 giri/min, 15 programmi di lavaggio',
                'modalita_installazione' => 'Collegare tubo carico acqua, tubo scarico e presa elettrica 220V',
                'modalita_uso' => 'Selezionare programma, temperatura e velocità centrifuga dal display',
                'prezzo' => 599.99,
                'staff_assegnato_id' => 2 // Assegnato a Giulia Bianchi (staffstaff)
            ],
            [
                'nome' => 'Lavastoviglie AquaClean Deluxe', 
                'modello' => 'LD-2024-002',
                'descrizione' => 'Lavastoviglie da incasso 12 coperti con sistema di asciugatura avanzato',
                'categoria' => 'lavastoviglie',
                'note_tecniche' => 'Classe energetica A++, consumo 9.5L per ciclo, 6 programmi',
                'modalita_installazione' => 'Installazione ad incasso, collegamento idraulico ed elettrico',
                'modalita_uso' => 'Caricare stoviglie, aggiungere detersivo, selezionare programma',
                'prezzo' => 799.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Forno Multifunzione ChefMaster',
                'modello' => 'FM-2024-003', 
                'descrizione' => 'Forno elettrico multifunzione con grill e ventilazione forzata',
                'categoria' => 'forno',
                'note_tecniche' => 'Capacità 65L, temperatura max 250°C, 8 funzioni di cottura',
                'modalita_installazione' => 'Installazione ad incasso, collegamento elettrico 220V monofase',
                'modalita_uso' => 'Preriscaldare, impostare temperatura e funzione, inserire alimenti',
                'prezzo' => 899.99,
                'staff_assegnato_id' => 4 // Assegnato ad Anna Neri (staff2)
            ],
            [
                'nome' => 'Frigorifero CoolFresh XL',
                'modello' => 'CF-2024-004',
                'descrizione' => 'Frigorifero combinato No Frost con dispenser acqua integrato',
                'categoria' => 'frigorifero', 
                'note_tecniche' => 'Capacità 350L, classe A++, tecnologia No Frost, LED interno',
                'modalita_installazione' => 'Posizionare su superficie piana, collegare alla rete elettrica',
                'modalita_uso' => 'Regolare temperatura frigo/freezer, utilizzare dispenser acqua',
                'prezzo' => 1299.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Asciugatrice DryMax Eco',
                'modello' => 'DM-2024-005',
                'descrizione' => 'Asciugatrice a pompa di calore, capacità 9kg, sensori di umidità',
                'categoria' => 'asciugatrice',
                'note_tecniche' => 'Classe energetica A+++, pompa di calore, 16 programmi automatici',
                'modalita_installazione' => 'Collegamento elettrico, posizione ventilata o con tubo scarico',
                'modalita_uso' => 'Caricare biancheria, selezionare programma in base al tessuto',
                'prezzo' => 749.99,
                'staff_assegnato_id' => 2
            ]
        ];

        foreach ($prodotti as $prodotto) {
            Prodotto::create($prodotto);
        }

        // === CREAZIONE MALFUNZIONAMENTI DI ESEMPIO ===
        echo "Creazione database malfunzionamenti...\n";

        $malfunzionamenti = [
            // === MALFUNZIONAMENTI LAVATRICE (prodotto_id = 1) ===
            [
                'prodotto_id' => 1,
                'titolo' => 'Lavatrice non centrifuga',
                'descrizione' => 'La lavatrice completa il ciclo di lavaggio ma non esegue la centrifuga finale',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare che il carico sia bilanciato\n2. Controllare il filtro scarico\n3. Verificare la pompa di scarico\n4. Controllare la cinghia di trasmissione\n5. Se necessario, sostituire il modulo elettronico",
                'strumenti_necessari' => 'Chiavi inglesi, multimetro, torcia',
                'tempo_stimato' => 45,
                'difficolta' => 'media',
                'numero_segnalazioni' => 12,
                'prima_segnalazione' => '2024-01-15',
                'ultima_segnalazione' => '2024-07-20',
                'creato_da' => 2, // Staff Giulia Bianchi
                'modificato_da' => 2
            ],
            [
                'prodotto_id' => 1,
                'titolo' => 'Perdita acqua dal cestello',
                'descrizione' => 'Si nota perdita di acqua nella parte inferiore della lavatrice durante il lavaggio',
                'gravita' => 'critica',
                'soluzione' => "1. Spegnere immediatamente la lavatrice\n2. Chiudere il rubinetto dell'acqua\n3. Controllare le guarnizioni dello sportello\n4. Verificare i tubi di collegamento\n5. Sostituire la guarnizione se danneggiata",
                'strumenti_necessari' => 'Guarnizione di ricambio, pinze, chiavi',
                'tempo_stimato' => 60,
                'difficolta' => 'difficile',
                'numero_segnalazioni' => 8,
                'prima_segnalazione' => '2024-02-10',
                'ultima_segnalazione' => '2024-07-15',
                'creato_da' => 2,
                'modificato_da' => 4 // Modificato da Anna Neri
            ],
            [
                'prodotto_id' => 1,
                'titolo' => 'Rumore eccessivo durante lavaggio',
                'descrizione' => 'La lavatrice produce rumori anomali durante il ciclo di lavaggio e centrifuga',
                'gravita' => 'media',
                'soluzione' => "1. Verificare che la lavatrice sia in bolla\n2. Controllare che non ci siano oggetti estranei nel cestello\n3. Verificare le molle di sospensione\n4. Controllare i cuscinetti del cestello\n5. Lubrificare le parti mobili se necessario",
                'strumenti_necessari' => 'Livella, lubrificante, chiavi varie',
                'tempo_stimato' => 30,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 15,
                'prima_segnalazione' => '2024-01-20',
                'ultima_segnalazione' => '2024-07-25',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI LAVASTOVIGLIE (prodotto_id = 2) ===
            [
                'prodotto_id' => 2,
                'titolo' => 'Stoviglie non si asciugano',
                'descrizione' => 'Al termine del ciclo le stoviglie rimangono bagnate o con gocce d\'acqua',
                'gravita' => 'media',
                'soluzione' => "1. Verificare il livello del brillantante\n2. Controllare la temperatura dell'acqua in ingresso\n3. Pulire i filtri di scarico\n4. Verificare il funzionamento della resistenza di asciugatura\n5. Controllare la ventola di circolazione aria",
                'strumenti_necessari' => 'Brillantante, spazzola per pulizia, multimetro',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 6,
                'prima_segnalazione' => '2024-03-05',
                'ultima_segnalazione' => '2024-06-12',
                'creato_da' => 2
            ],
            [
                'prodotto_id' => 2,
                'titolo' => 'Macchie bianche sui bicchieri',
                'descrizione' => 'I bicchieri presentano macchie bianche opache dopo il lavaggio',
                'gravita' => 'bassa',
                'soluzione' => "1. Controllare la durezza dell'acqua\n2. Regolare il dosaggio del sale rigenerante\n3. Pulire il contenitore del sale\n4. Verificare il dosaggio del detersivo\n5. Utilizzare un ciclo di pulizia con aceto bianco",
                'strumenti_necessari' => 'Sale rigenerante, aceto bianco, spugna',
                'tempo_stimato' => 15,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 20,
                'prima_segnalazione' => '2024-02-01',
                'ultima_segnalazione' => '2024-07-30',
                'creato_da' => 4 // Anna Neri
            ],

            // === MALFUNZIONAMENTI FORNO (prodotto_id = 3) ===
            [
                'prodotto_id' => 3,
                'titolo' => 'Forno non raggiunge la temperatura',
                'descrizione' => 'Il forno impiega troppo tempo per raggiungere la temperatura impostata o non la raggiunge',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare la calibrazione del termostato\n2. Controllare le resistenze elettriche\n3. Verificare il sensore di temperatura\n4. Controllare la guarnizione dello sportello\n5. Sostituire componenti difettosi",
                'strumenti_necessari' => 'Multimetro, termometro da forno, chiavi inglesi',
                'tempo_stimato' => 90,
                'difficolta' => 'esperto',
                'numero_segnalazioni' => 4,
                'prima_segnalazione' => '2024-04-10',
                'ultima_segnalazione' => '2024-07-05',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 3,
                'titolo' => 'Grill non funziona',
                'descrizione' => 'La funzione grill del forno non si attiva o scalda insufficientemente',
                'gravita' => 'media',
                'soluzione' => "1. Verificare la selezione della funzione grill sul pannello\n2. Controllare la resistenza grill superiore\n3. Testare il relè di attivazione grill\n4. Verificare i collegamenti elettrici\n5. Sostituire la resistenza se bruciata",
                'strumenti_necessari' => 'Multimetro, tester continuità, cacciaviti',
                'tempo_stimato' => 40,
                'difficolta' => 'media',
                'numero_segnalazioni' => 3,
                'prima_segnalazione' => '2024-05-20',
                'ultima_segnalazione' => '2024-07-12',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI FRIGORIFERO (prodotto_id = 4) ===
            [
                'prodotto_id' => 4,
                'titolo' => 'Frigorifero fa troppo rumore',
                'descrizione' => 'Il compressore del frigorifero produce rumori eccessivi o intermittenti',
                'gravita' => 'media',
                'soluzione' => "1. Verificare che il frigorifero sia in bolla\n2. Controllare che non tocchi mobili o pareti\n3. Pulire la griglia posteriore\n4. Verificare le ventole di raffreddamento\n5. Controllare il compressore",
                'strumenti_necessari' => 'Livella, aspirapolvere, chiavi',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 10,
                'prima_segnalazione' => '2024-03-15',
                'ultima_segnalazione' => '2024-07-18',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 4,
                'titolo' => 'Dispenser acqua non funziona',
                'descrizione' => 'Il dispenser integrato per l\'acqua non eroga o eroga pochissima acqua',
                'gravita' => 'bassa',
                'soluzione' => "1. Verificare che il serbatoio interno sia pieno\n2. Controllare il filtro dell'acqua\n3. Verificare la pompa del dispenser\n4. Controllare i tubi di collegamento\n5. Sostituire il filtro se intasato",
                'strumenti_necessari' => 'Filtro di ricambio, chiavi per tubi',
                'tempo_stimato' => 30,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 5,
                'prima_segnalazione' => '2024-04-20',
                'ultima_segnalazione' => '2024-06-30',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI ASCIUGATRICE (prodotto_id = 5) ===
            [
                'prodotto_id' => 5,
                'titolo' => 'Biancheria rimane umida',
                'descrizione' => 'Al termine del ciclo la biancheria non è completamente asciutta',
                'gravita' => 'alta',
                'soluzione' => "1. Pulire il filtro della lanugine\n2. Verificare il tubo di scarico condensa\n3. Controllare i sensori di umidità\n4. Verificare la pompa di calore\n5. Pulire lo scambiatore di calore",
                'strumenti_necessari' => 'Spazzola, aspirapolvere, panno umido',
                'tempo_stimato' => 35,
                'difficolta' => 'media',
                'numero_segnalazioni' => 7,
                'prima_segnalazione' => '2024-05-01',
                'ultima_segnalazione' => '2024-07-22',
                'creato_da' => 2
            ],
            [
                'prodotto_id' => 5,
                'titolo' => 'Serbatoio condensa sempre pieno',
                'descrizione' => 'Il serbatoio della condensa si riempie velocemente anche con pochi capi',
                'gravita' => 'media',
                'soluzione' => "1. Verificare che il tubo di scarico diretto non sia collegato\n2. Controllare che il serbatoio sia posizionato correttamente\n3. Verificare perdite nel circuito della condensa\n4. Controllare il sensore di livello del serbatoio\n5. Pulire il circuito di scarico condensa",
                'strumenti_necessari' => 'Panno, spazzola piccola',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 4,
                'prima_segnalazione' => '2024-06-10',
                'ultima_segnalazione' => '2024-07-28',
                'creato_da' => 2
            ]
        ];

        foreach ($malfunzionamenti as $malfunzionamento) {
            Malfunzionamento::create($malfunzionamento);
        }

        // === RIEPILOGO FINALE ===
        echo "\n=== DATABASE POPOLATO CON SUCCESSO! ===\n";
        echo "Utenti creati (tutti con password: dNWRdNWR):\n";
        echo "- adminadmin (Amministratore - Livello 4)\n";
        echo "- staffstaff (Staff Aziendale - Livello 3)\n"; 
        echo "- tecntecn (Tecnico Centro Assistenza - Livello 2)\n";
        echo "- staff2, tecnico2, tecnico3 (utenti aggiuntivi per test)\n";
        echo "\nDati inseriti:\n";
        echo "- Centri Assistenza: " . count($centri) . "\n";
        echo "- Prodotti: " . count($prodotti) . "\n";
        echo "- Malfunzionamenti: " . count($malfunzionamenti) . "\n";
        echo "\nIl sistema è pronto per essere testato!\n";
    }
}