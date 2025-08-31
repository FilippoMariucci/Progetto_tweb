<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CentroAssistenza;
use App\Models\Prodotto;
use App\Models\Malfunzionamento;

/**
 * DatabaseSeeder Completo per Sistema Assistenza Tecnica
 * 
 * Popola il database con dati di esempio completi per testare tutte le funzionalità:
 * - Utenti con tutti i livelli di accesso (come da specifiche progetto)
 * - Centri assistenza distribuiti sul territorio italiano
 * - Catalogo completo di elettrodomestici di ogni categoria
 * - Malfunzionamenti realistici con soluzioni tecniche dettagliate
 * 
 * Password per tutti gli utenti: dNWRdNWR (primi 4 caratteri SSH ripetuti)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disabilita controlli foreign key per truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Svuota tutte le tabelle per ricominciare da zero
        DB::table('malfunzionamenti')->truncate();
        DB::table('prodotti')->truncate();
        DB::table('users')->truncate();
        DB::table('centri_assistenza')->truncate();
        
        // Riabilita controlli foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "=== POPOLAMENTO DATABASE SISTEMA ASSISTENZA TECNICA ===\n";

        // === CREAZIONE UTENTI ===
        echo "Creazione utenti del sistema...\n";
        
        $utenti = [
            // UTENTI OBBLIGATORI (come da specifiche del progetto)
            [
                'username' => 'adminadmin', // Livello 4 - Amministratore
                'nome' => 'Mario',
                'cognome' => 'Rossi',
                'password' => Hash::make('dNWRdNWR'), // Password SSH primi 4 caratteri ripetuti
                'livello_accesso' => '4',
                'data_nascita' => '1975-05-15'
            ],
            [
                'username' => 'staffstaff', // Livello 3 - Staff Aziendale
                'nome' => 'Giulia',
                'cognome' => 'Bianchi',
                'password' => Hash::make('dNWRdNWR'),
                'livello_accesso' => '3',
                'data_nascita' => '1985-08-22'
            ],
            [
                'username' => 'tecntecn', // Livello 2 - Tecnico
                'nome' => 'Francesco',
                'cognome' => 'Verdi',
                'password' => Hash::make('dNWRdNWR'),
                'livello_accesso' => '2',
                'data_nascita' => '1990-03-10',
                'specializzazione' => 'Elettrodomestici da cucina',
                'centro_assistenza_id' => 1 // FK al centro assistenza
            ],
            
            // UTENTI AGGIUNTIVI PER TESTING COMPLETO
            [
                'username' => 'staff2',
                'nome' => 'Anna',
                'cognome' => 'Neri',
                'password' => Hash::make('dNWRdNWR'),
                'livello_accesso' => '3',
                'data_nascita' => '1988-11-03'
            ],
            [
                'username' => 'tecnico2',
                'nome' => 'Luca',
                'cognome' => 'Gialli',
                'password' => Hash::make('dNWRdNWR'),
                'livello_accesso' => '2',
                'data_nascita' => '1987-07-18',
                'specializzazione' => 'Lavabiancheria e asciugatrici',
                'centro_assistenza_id' => 2 // FK al centro Milano
            ],
            [
                'username' => 'tecnico3',
                'nome' => 'Sara',
                'cognome' => 'Blu',
                'password' => Hash::make('dNWRdNWR'),
                'livello_accesso' => '2',
                'data_nascita' => '1992-12-25',
                'specializzazione' => 'Refrigerazione e climatizzazione',
                'centro_assistenza_id' => 3 // FK al centro Napoli
            ]
        ];

        foreach ($utenti as $utente) {
            User::create($utente);
        }

        // === CREAZIONE CENTRI ASSISTENZA ===
        echo "Creazione centri assistenza sul territorio...\n";
        
        $centri = [
            [
                'nome' => 'Centro Assistenza Roma Nord',
                'indirizzo' => 'Via Flaminia 245, 00196 Roma RM',
                'citta' => 'Roma',
                'provincia' => 'RM',
                'cap' => '00196',
                'telefono' => '+39 06 3610 1234',
                'email' => 'roma.nord@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Milano Est',
                'indirizzo' => 'Viale Monza 132, 20125 Milano MI',
                'citta' => 'Milano',
                'provincia' => 'MI', 
                'cap' => '20125',
                'telefono' => '+39 02 2845 5678',
                'email' => 'milano.est@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Napoli Centro',
                'indirizzo' => 'Via Toledo 289, 80132 Napoli NA',
                'citta' => 'Napoli',
                'provincia' => 'NA',
                'cap' => '80132',
                'telefono' => '+39 081 551 9012',
                'email' => 'napoli.centro@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Torino Sud',
                'indirizzo' => 'Corso Unione Sovietica 145, 10134 Torino TO',
                'citta' => 'Torino',
                'provincia' => 'TO',
                'cap' => '10134',
                'telefono' => '+39 011 347 3456',
                'email' => 'torino.sud@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Bologna Ovest',
                'indirizzo' => 'Via Andrea Costa 67, 40134 Bologna BO',
                'citta' => 'Bologna',
                'provincia' => 'BO',
                'cap' => '40134',
                'telefono' => '+39 051 478 7890',
                'email' => 'bologna.ovest@assistenza.it'
            ],
            [
                'nome' => 'Centro Assistenza Firenze',
                'indirizzo' => 'Via Senese 178, 50124 Firenze FI',
                'citta' => 'Firenze',
                'provincia' => 'FI',
                'cap' => '50124',
                'telefono' => '+39 055 234 1122',
                'email' => 'firenze@assistenza.it'
            ]
        ];

        foreach ($centri as $centro) {
            CentroAssistenza::create($centro);
        }

        // === CREAZIONE CATALOGO PRODOTTI COMPLETO ===
        echo "Creazione catalogo prodotti completo...\n";
        
        $prodotti = [
            // === CATEGORIA LAVATRICI ===
            [
                'nome' => 'Lavatrice EcoWash Pro',
                'modello' => 'EW-2024-001',
                'descrizione' => 'Lavatrice a carica frontale da 9kg con tecnologia inverter e vapore igienizzante',
                'categoria' => 'lavatrice',
                'note_tecniche' => 'Capacità 9kg, classe A+++, 1400 giri/min, 16 programmi, tecnologia vapore',
                'modalita_installazione' => 'Collegamento idraulico (carico/scarico), elettrico 220V, livellamento',
                'modalita_uso' => 'Caricare max 9kg, dosare detersivo, selezionare programma appropriato',
                'prezzo' => 599.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Lavatrice SlimWash Compact',
                'modello' => 'SW-2024-002',
                'descrizione' => 'Lavatrice slim da 7kg, profondità ridotta 40cm per spazi ristretti',
                'categoria' => 'lavatrice',
                'note_tecniche' => 'Capacità 7kg, classe A++, 1200 giri/min, profondità 40cm, 12 programmi',
                'modalita_installazione' => 'Spazio minimo 60x40x85cm, collegamenti idraulici standard',
                'modalita_uso' => 'Ideale per 3-4 persone, carico massimo 7kg, cicli eco disponibili',
                'prezzo' => 449.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Lavatrice TopLoad Family',
                'modello' => 'TF-2024-003',
                'descrizione' => 'Lavatrice a carica dall\'alto da 10kg per famiglie numerose',
                'categoria' => 'lavatrice',
                'note_tecniche' => 'Capacità 10kg, carica dall\'alto, classe A++, 1300 giri/min',
                'modalita_installazione' => 'Spazio superiore libero per apertura, collegamenti standard',
                'modalita_uso' => 'Caricare dall\'alto, bilanciare carico, programmi delicati per lana',
                'prezzo' => 659.99,
                'staff_assegnato_id' => 2
            ],

            // === CATEGORIA LAVASTOVIGLIE ===
            [
                'nome' => 'Lavastoviglie SilentClean',
                'modello' => 'SC-2024-004',
                'descrizione' => 'Lavastoviglie da incasso 60cm ultra-silenziosa con 3° cestello',
                'categoria' => 'lavastoviglie',
                'note_tecniche' => '14 coperti, classe A+++, 42dB, 3° cestello posate, 8 programmi',
                'modalita_installazione' => 'Incasso sotto piano cucina, collegamenti acqua calda/fredda',
                'modalita_uso' => 'Carico max 14 coperti, sale rigenerante, brillantante, programmi eco',
                'prezzo' => 549.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Lavastoviglie Compact 45',
                'modello' => 'CP-2024-005',
                'descrizione' => 'Lavastoviglie slim da 45cm per cucine piccole, 10 coperti',
                'categoria' => 'lavastoviglie',
                'note_tecniche' => '10 coperti, larghezza 45cm, classe A++, 6 programmi',
                'modalita_installazione' => 'Incasso o libera installazione, spazio ridotto',
                'modalita_uso' => 'Ideale per 2-3 persone, cicli rapidi disponibili',
                'prezzo' => 399.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA FRIGORIFERI ===
            [
                'nome' => 'Frigorifero CoolFresh XL',
                'modello' => 'CF-2024-006',
                'descrizione' => 'Frigorifero combinato No Frost da 400L con dispenser acqua',
                'categoria' => 'frigorifero',
                'note_tecniche' => 'Capacità 400L, No Frost, classe A++, LED, dispenser acqua/ghiaccio',
                'modalita_installazione' => 'Superficie piana, areazione posteriore, collegamento idrico dispenser',
                'modalita_uso' => 'Regolazione temperature separate, filtro acqua ogni 6 mesi',
                'prezzo' => 1299.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Frigorifero Side by Side Premium',
                'modello' => 'SBS-2024-007',
                'descrizione' => 'Frigorifero americano 550L con tecnologia Total No Frost',
                'categoria' => 'frigorifero',
                'note_tecniche' => 'Capacità 550L, side-by-side, classe A+, display touch, Wi-Fi',
                'modalita_installazione' => 'Spazio minimo 92x70x178cm, ventilazione laterale',
                'modalita_uso' => 'Controllo tramite display touch, connessione app smartphone',
                'prezzo' => 1899.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Frigorifero Mini Bar Office',
                'modello' => 'MB-2024-008',
                'descrizione' => 'Frigorifero compatto da 85L per uffici e monolocali',
                'categoria' => 'frigorifero',
                'note_tecniche' => 'Capacità 85L, classe A+, silenzioso 39dB, scomparto freezer',
                'modalita_installazione' => 'Plug and play, superficie stabile',
                'modalita_uso' => 'Ideale per bevande e snack, regolazione temperatura manuale',
                'prezzo' => 199.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA FORNI ===
            [
                'nome' => 'Forno Multifunzione Chef Pro',
                'modello' => 'CP-2024-009',
                'descrizione' => 'Forno elettrico multifunzione da incasso con pirolisi',
                'categoria' => 'forno',
                'note_tecniche' => '65L, 10 funzioni cottura, pirolisi, classe A, display touch',
                'modalita_installazione' => 'Incasso in colonna o sotto piano, collegamento 220V',
                'modalita_uso' => 'Preriscaldamento, selezione programmi, pulizia pirolitica automatica',
                'prezzo' => 799.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Forno a Vapore SteamBake',
                'modello' => 'SB-2024-010',
                'descrizione' => 'Forno combinato vapore per cotture salutari',
                'categoria' => 'forno',
                'note_tecniche' => '45L, cottura vapore+convezione, serbatoio 1.2L, 15 programmi',
                'modalita_installazione' => 'Incasso, collegamento acqua opzionale per serbatoio fisso',
                'modalita_uso' => 'Riempire serbatoio acqua, programmi automatici per ogni alimento',
                'prezzo' => 1199.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA ASCIUGATRICI ===
            [
                'nome' => 'Asciugatrice DryMax Eco',
                'modello' => 'DM-2024-011',
                'descrizione' => 'Asciugatrice a pompa di calore da 9kg con sensori intelligenti',
                'categoria' => 'asciugatrice',
                'note_tecniche' => 'Pompa di calore, classe A+++, 9kg, 16 programmi, sensori umidità',
                'modalita_installazione' => 'Ventilazione o scarico diretto condensa, collegamento elettrico',
                'modalita_uso' => 'Pulire filtro lanugine, svuotare serbatoio condensa, programmi automatici',
                'prezzo' => 749.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Asciugatrice Rapid Dry',
                'modello' => 'RD-2024-012',
                'descrizione' => 'Asciugatrice a condensazione rapida da 8kg',
                'categoria' => 'asciugatrice',
                'note_tecniche' => 'Condensazione, classe A++, 8kg, ciclo rapido 30min, anti-pieghe',
                'modalita_installazione' => 'Installazione libera o a colonna, scarico condensa in serbatoio',
                'modalita_uso' => 'Caricare secondo tipo tessuto, cicli delicati per capi sensibili',
                'prezzo' => 549.99,
                'staff_assegnato_id' => 2
            ],

            // === CATEGORIA PIANI COTTURA ===
            [
                'nome' => 'Piano Cottura Induzione FlexCook',
                'modello' => 'FC-2024-013',
                'descrizione' => 'Piano cottura a induzione da 60cm con zona flessibile',
                'categoria' => 'piano_cottura',
                'note_tecniche' => '4 zone induzione, zona flex, controlli touch, timer individuale',
                'modalita_installazione' => 'Incasso nel piano cucina, collegamento elettrico 380V',
                'modalita_uso' => 'Solo pentole compatibili induzione, controllo temperatura preciso',
                'prezzo' => 899.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Piano Cottura Gas Tradizionale',
                'modello' => 'GT-2024-014',
                'descrizione' => 'Piano cottura a gas da 75cm con griglie in ghisa',
                'categoria' => 'piano_cottura',
                'note_tecniche' => '5 fuochi gas, griglie ghisa, accensione elettrica, sicurezza gas',
                'modalita_installazione' => 'Collegamento gas metano/GPL, areazione ambiente',
                'modalita_uso' => 'Verificare fiamma blu, pulizia griglie, controllo perdite gas',
                'prezzo' => 649.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA CAPPE ===
            [
                'nome' => 'Cappa Aspirante SilentPower',
                'modello' => 'SP-2024-015',
                'descrizione' => 'Cappa a parete da 60cm con motore extra-silenzioso',
                'categoria' => 'cappa',
                'note_tecniche' => '600m³/h, 3 velocità, LED, filtri antigrasso lavabili, 52dB',
                'modalita_installazione' => 'Fissaggio a parete, canalizzazione esterna o ricircolo',
                'modalita_uso' => 'Accendere prima della cottura, pulire filtri ogni mese',
                'prezzo' => 349.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Cappa Isola Design Premium',
                'modello' => 'ID-2024-016',
                'descrizione' => 'Cappa sospesa per isole cucina con design moderno',
                'categoria' => 'cappa',
                'note_tecniche' => '900m³/h, controllo remoto, illuminazione LED regolabile',
                'modalita_installazione' => 'Sospensione a soffitto, canalizzazione dedicata',
                'modalita_uso' => 'Controllo tramite telecomando, regolazione automatica potenza',
                'prezzo' => 1299.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA MICROONDE ===
            [
                'nome' => 'Microonde CombiSteam',
                'modello' => 'CS-2024-017',
                'descrizione' => 'Forno microonde combinato con grill e vapore da incasso',
                'categoria' => 'microonde',
                'note_tecniche' => '25L, 900W microonde, grill 1000W, vapore, 10 programmi automatici',
                'modalita_installazione' => 'Incasso in colonna o pensile, ventilazione laterale',
                'modalita_uso' => 'Cottura microonde, grill, vapore, programmi combinati',
                'prezzo' => 499.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Microonde Basic Home',
                'modello' => 'BH-2024-018',
                'descrizione' => 'Microonde semplice da tavolo per uso domestico',
                'categoria' => 'microonde',
                'note_tecniche' => '20L, 700W, 5 livelli potenza, timer meccanico, piatto girevole',
                'modalita_installazione' => 'Posizionamento su superficie stabile, presa standard',
                'modalita_uso' => 'Riscaldamento cibi, scongelamento, cottura semplice',
                'prezzo' => 119.99,
                'staff_assegnato_id' => 4
            ],

            // === CATEGORIA CONDIZIONATORI ===
            [
                'nome' => 'Condizionatore ClimaPro Inverter',
                'modello' => 'CP-2024-019',
                'descrizione' => 'Climatizzatore inverter da 12000 BTU con Wi-Fi',
                'categoria' => 'condizionatore',
                'note_tecniche' => '12000 BTU, inverter, classe A+++, Wi-Fi, filtri autopulenti',
                'modalita_installazione' => 'Unità interna a parete, unità esterna su balcone/terrazzo',
                'modalita_uso' => 'Controllo app smartphone, modalità eco, timer programmabile',
                'prezzo' => 849.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Condizionatore Dual Split Home',
                'modello' => 'DS-2024-020',
                'descrizione' => 'Sistema dual split per climatizzare due ambienti',
                'categoria' => 'condizionatore',
                'note_tecniche' => '9000+12000 BTU, dual split, controlli separati, R32 ecologico',
                'modalita_installazione' => 'Due unità interne, una esterna, tubazioni refrigerante',
                'modalita_uso' => 'Controllo indipendente per ambiente, manutenzione filtri',
                'prezzo' => 1399.99,
                'staff_assegnato_id' => 4
            ],

            // === PICCOLI ELETTRODOMESTICI ===
            [
                'nome' => 'Robot Aspirapolvere SmartClean',
                'modello' => 'SC-2024-021',
                'descrizione' => 'Robot aspirapolvere con mappatura laser e svuotamento automatico',
                'categoria' => 'aspirapolvere',
                'note_tecniche' => 'Mappatura LiDAR, autonomia 120min, svuotamento automatico, app',
                'modalita_installazione' => 'Posizionare base di ricarica, download app mobile',
                'modalita_uso' => 'Programmazione tramite app, svuotamento automatico sacchetto',
                'prezzo' => 699.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Ferro da Stiro ProIron',
                'modello' => 'PI-2024-022',
                'descrizione' => 'Sistema stiratura con caldaia separata ad alta pressione',
                'categoria' => 'ferro_stiro',
                'note_tecniche' => 'Caldaia 1.5L, pressione 6 bar, piastra ceramica, anti-calcare',
                'modalita_installazione' => 'Asse da stiro robusto, presa elettrica dedicata',
                'modalita_uso' => 'Riempire caldaia con acqua demineralizzata, preriscaldamento',
                'prezzo' => 299.99,
                'staff_assegnato_id' => 2
            ],
            [
                'nome' => 'Macchina Caffè DeluxeBrew',
                'modello' => 'DB-2024-023',
                'descrizione' => 'Macchina espresso automatica con macinacaffè integrato',
                'categoria' => 'macchina_caffe',
                'note_tecniche' => 'Macinacaffè ceramico, 15 bar, cappuccinatore automatico, display LCD',
                'modalita_installazione' => 'Piano d\'appoggio, collegamento acqua o serbatoio',
                'modalita_uso' => 'Chicchi di caffè in grani, pulizia automatica giornaliera',
                'prezzo' => 1199.99,
                'staff_assegnato_id' => 2
            ],

            // === CATEGORIA SCALDACQUA ===
            [
                'nome' => 'Scaldabagno Elettrico EcoHeat',
                'modello' => 'EH-2024-024',
                'descrizione' => 'Boiler elettrico da 80L con resistenza ceramica',
                'categoria' => 'scaldabagno',
                'note_tecniche' => '80L, resistenza ceramica anti-calcare, classe B, termostato digitale',
                'modalita_installazione' => 'Fissaggio a parete, collegamenti idraulici, elettrico 220V',
                'modalita_uso' => 'Temperatura ottimale 60°C, manutenzione anodo magnesio annuale',
                'prezzo' => 399.99,
                'staff_assegnato_id' => 4
            ],
            [
                'nome' => 'Caldaia Condensazione GasEfficient',
                'modello' => 'GE-2024-025',
                'descrizione' => 'Caldaia murale a condensazione per riscaldamento e acqua calda',
                'categoria' => 'caldaia',
                'note_tecniche' => '24kW, condensazione, modulazione 1:5, classe A+, controllo remoto',
                'modalita_installazione' => 'Solo tecnico abilitato, scarico fumi condensazione',
                'modalita_uso' => 'Controllo termostato ambiente, manutenzione annuale obbligatoria',
                'prezzo' => 1599.99,
                'staff_assegnato_id' => 4
            ]
        ];

        foreach ($prodotti as $prodotto) {
            Prodotto::create($prodotto);
        }

        // === CREAZIONE MALFUNZIONAMENTI REALISTICI ===
        echo "Creazione database malfunzionamenti completo...\n";

        $malfunzionamenti = [
            // === MALFUNZIONAMENTI LAVATRICE EcoWash Pro (ID: 1) ===
            [
                'prodotto_id' => 1,
                'titolo' => 'Lavatrice non centrifuga',
                'descrizione' => 'La lavatrice completa il ciclo ma la biancheria rimane molto bagnata',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare che il carico sia equilibrato nel cestello\n2. Controllare il filtro di scarico per intasamenti\n3. Verificare il funzionamento della pompa di scarico\n4. Controllare la cinghia di trasmissione del motore\n5. Testare il modulo elettronico di controllo\n6. Sostituire componenti difettosi se necessario",
                'strumenti_necessari' => 'Chiavi inglesi 10-13mm, multimetro digitale, torcia LED',
                'tempo_stimato' => 60,
                'difficolta' => 'media',
                'numero_segnalazioni' => 15,
                'prima_segnalazione' => '2024-01-15',
                'ultima_segnalazione' => '2024-07-28',
                'creato_da' => 2
            ],
            [
                'prodotto_id' => 1,
                'titolo' => 'Perdita acqua dalla base',
                'descrizione' => 'Perdita di acqua visibile sotto la lavatrice durante funzionamento',
                'gravita' => 'critica',
'soluzione' => "PROCEDURA DI EMERGENZA:\n1. Spegnere immediatamente e staccare spina\n2. Chiudere rubinetto acqua\n3. Controllare guarnizioni dello sportello\n4. Verificare tubi di collegamento (carico/scarico)\n5. Ispezionare vasca interna per crepe\n6. Controllare gruppo cuscinetti cestello\n7. Sostituire guarnizioni danneggiate",
                'strumenti_necessari' => 'Torcia, specchietto ispezione, guarnizioni ricambio',
                'tempo_stimato' => 90,
                'difficolta' => 'difficile',
                'numero_segnalazioni' => 8,
                'prima_segnalazione' => '2024-02-20',
                'ultima_segnalazione' => '2024-07-15',
                'creato_da' => 2
            ],
            [
                'prodotto_id' => 1,
                'titolo' => 'Odore sgradevole dal cestello',
                'descrizione' => 'Cattivo odore persistente che si trasferisce ai vestiti',
                'gravita' => 'media',
                'soluzione' => "1. Ciclo pulizia 90°C con bicarbonato\n2. Pulire guarnizione sportello\n3. Lasciare sportello aperto dopo lavaggio\n4. Pulire filtro scarico e cassetto detersivo\n5. Prodotti anti-odore mensili",
                'strumenti_necessari' => 'Bicarbonato, detergente specifico, spugna, guanti',
                'tempo_stimato' => 30,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 22,
                'prima_segnalazione' => '2024-01-08',
                'ultima_segnalazione' => '2024-07-30',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI LAVATRICE SlimWash (ID: 2) ===
            [
                'prodotto_id' => 2,
                'titolo' => 'Vibrazione eccessiva durante centrifuga',
                'descrizione' => 'La lavatrice vibra e si sposta durante la centrifuga',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare livellamento con bolla\n2. Regolare piedini antivibrazione\n3. Verificare rimozione viti trasporto\n4. Controllare carico max 7kg\n5. Bilanciare carico nel cestello\n6. Verificare usura ammortizzatori",
                'strumenti_necessari' => 'Livella a bolla, chiave regolazione piedini',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 18,
                'prima_segnalazione' => '2024-02-05',
                'ultima_segnalazione' => '2024-07-25',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI LAVATRICE TopLoad (ID: 3) ===
            [
                'prodotto_id' => 3,
                'titolo' => 'Coperchio non si apre dopo ciclo',
                'descrizione' => 'Meccanismo sicurezza coperchio rimane bloccato a fine ciclo',
                'gravita' => 'media',
                'soluzione' => "1. Attendere 2-3 minuti dopo fine ciclo\n2. Verificare display mostri 'Fine'\n3. Controllare sensore blocco coperchio\n4. Verificare assenza acqua residua\n5. Reset programma (Start 5 sec)\n6. Chiamare assistenza se persiste",
                'strumenti_necessari' => 'Manuale utente, reset programmatore',
                'tempo_stimato' => 15,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 12,
                'prima_segnalazione' => '2024-03-12',
                'ultima_segnalazione' => '2024-07-20',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI LAVASTOVIGLIE SilentClean (ID: 4) ===
            [
                'prodotto_id' => 4,
                'titolo' => 'Stoviglie rimangono macchiate',
                'descrizione' => 'Macchie bianche o aloni sui bicchieri dopo lavaggio',
                'gravita' => 'bassa',
                'soluzione' => "1. Controllare durezza acqua e dosaggio sale\n2. Verificare livello brillantante\n3. Pulire bracci aspersori\n4. Detergente anti-calcare specifico\n5. Ciclo pulizia mensile",
                'strumenti_necessari' => 'Sale rigenerante, brillantante, detergente anti-calcare',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 35,
                'prima_segnalazione' => '2024-01-20',
                'ultima_segnalazione' => '2024-07-30',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 4,
                'titolo' => 'Lavastoviglie non scarica acqua',
                'descrizione' => 'Acqua stagnante sul fondo al termine del ciclo',
                'gravita' => 'alta',
                'soluzione' => "1. Controllare tubo scarico non piegato\n2. Pulire filtro di scarico\n3. Controllare pompa scarico per ostruzioni\n4. Verificare collegamento sifone lavello\n5. Testare pompa con multimetro\n6. Sostituire pompa se guasta",
                'strumenti_necessari' => 'Chiavi, spugna, multimetro, pompa ricambio',
                'tempo_stimato' => 45,
                'difficolta' => 'media',
                'numero_segnalazioni' => 9,
                'prima_segnalazione' => '2024-03-08',
                'ultima_segnalazione' => '2024-07-12',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI LAVASTOVIGLIE Compact (ID: 5) ===
            [
                'prodotto_id' => 5,
                'titolo' => 'Lavaggio troppo rumoroso',
                'descrizione' => 'Rumori eccessivi durante il funzionamento',
                'gravita' => 'media',
                'soluzione' => "1. Verificare stoviglie non si tocchino\n2. Controllare posizionamento cestello\n3. Verificare rotazione libera bracci\n4. Controllare livellamento\n5. Ispezionare cuscinetti pompa",
                'strumenti_necessari' => 'Livella, torcia per ispezione',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 14,
                'prima_segnalazione' => '2024-02-15',
                'ultima_segnalazione' => '2024-07-18',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI FRIGORIFERO CoolFresh (ID: 6) ===
            [
                'prodotto_id' => 6,
                'titolo' => 'Frigorifero fa troppo rumore',
                'descrizione' => 'Compressore produce rumori eccessivi o intermittenti',
                'gravita' => 'media',
                'soluzione' => "1. Verificare perfetto livellamento\n2. Controllare non tocchi mobili/pareti\n3. Pulire griglia ventilazione posteriore\n4. Verificare ventole raffreddamento\n5. Controllare vibrazioni compressore\n6. Lubrificare cuscinetti se necessario",
                'strumenti_necessari' => 'Livella, aspirapolvere, lubrificante specifico',
                'tempo_stimato' => 30,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 28,
                'prima_segnalazione' => '2024-01-25',
                'ultima_segnalazione' => '2024-07-29',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 6,
                'titolo' => 'Dispenser acqua non eroga',
                'descrizione' => 'Dispenser non eroga acqua o eroga molto lentamente',
                'gravita' => 'bassa',
                'soluzione' => "1. Verificare serbatoio pieno\n2. Controllare filtro acqua (sostituire se intasato)\n3. Verificare pompa dispenser\n4. Pulire tubi collegamento\n5. Testare valvola erogazione\n6. Reset sistema (pulsante 10 sec)",
                'strumenti_necessari' => 'Filtro acqua ricambio, chiavi collegamenti',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 11,
                'prima_segnalazione' => '2024-04-02',
                'ultima_segnalazione' => '2024-07-10',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI FRIGORIFERO Side by Side (ID: 7) ===
            [
                'prodotto_id' => 7,
                'titolo' => 'Display touch non risponde',
                'descrizione' => 'Pannello controllo touch non risponde ai comandi',
                'gravita' => 'alta',
                'soluzione' => "1. Pulire display con panno asciutto\n2. Reset completo (due pulsanti 10 sec)\n3. Verificare alimentazione modulo\n4. Controllare collegamenti flat cable\n5. Aggiornare firmware\n6. Sostituire modulo display",
                'strumenti_necessari' => 'Panno microfibra, multimetro, software aggiornamento',
                'tempo_stimato' => 35,
                'difficolta' => 'difficile',
                'numero_segnalazioni' => 4,
                'prima_segnalazione' => '2024-06-01',
                'ultima_segnalazione' => '2024-07-22',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI FORNO Chef Pro (ID: 9) ===
            [
                'prodotto_id' => 9,
                'titolo' => 'Forno non raggiunge temperatura',
                'descrizione' => 'Impiega troppo tempo o non raggiunge temperatura impostata',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare calibrazione termostato\n2. Controllare resistenze elettriche\n3. Testare sensore temperatura\n4. Verificare guarnizione sportello\n5. Controllare ventilazione forzata\n6. Sostituire componenti difettosi",
                'strumenti_necessari' => 'Termometro forno, multimetro, chiavi inglesi',
                'tempo_stimato' => 75,
                'difficolta' => 'esperto',
                'numero_segnalazioni' => 7,
                'prima_segnalazione' => '2024-03-20',
                'ultima_segnalazione' => '2024-07-14',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 9,
                'titolo' => 'Pirolisi non si attiva',
                'descrizione' => 'Ciclo auto-pulizia non parte o si interrompe',
                'gravita' => 'media',
                'soluzione' => "1. Verificare porta completamente chiusa\n2. Rimuovere accessori dal vano\n3. Controllare blocco porta pirolisi\n4. Verificare temperatura ambiente (max 35°C)\n5. Controllare sensore sicurezza\n6. Reset programma",
                'strumenti_necessari' => 'Termometro ambiente, manuale istruzioni',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 5,
                'prima_segnalazione' => '2024-04-18',
                'ultima_segnalazione' => '2024-06-28',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI ASCIUGATRICE DryMax (ID: 11) ===
            [
                'prodotto_id' => 11,
                'titolo' => 'Biancheria rimane umida',
                'descrizione' => 'Al termine del ciclo biancheria non completamente asciutta',
                'gravita' => 'alta',
                'soluzione' => "1. Pulire filtro lanugine\n2. Pulire condensatore pompa calore\n3. Controllare tubo scarico condensa\n4. Verificare sensori umidità\n5. Controllare circolazione aria\n6. Verificare pompa di calore",
                'strumenti_necessari' => 'Spazzola morbida, aspirapolvere, panno umido',
                'tempo_stimato' => 40,
                'difficolta' => 'media',
                'numero_segnalazioni' => 13,
                'prima_segnalazione' => '2024-03-05',
                'ultima_segnalazione' => '2024-07-24',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI PIANO COTTURA INDUZIONE (ID: 13) ===
            [
                'prodotto_id' => 13,
                'titolo' => 'Zona induzione non rileva pentola',
                'descrizione' => 'Zone cottura non riconoscono pentole compatibili',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare pentola adatta induzione (test magnete)\n2. Controllare fondo pentola piatto e pulito\n3. Pulire superficie piano cottura\n4. Verificare diametro minimo 12cm\n5. Controllare sensore bobina\n6. Testare con pentola diversa",
                'strumenti_necessari' => 'Pentole test, magnete, detergente piano',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 19,
                'prima_segnalazione' => '2024-02-10',
                'ultima_segnalazione' => '2024-07-27',
                'creato_da' => 4
            ],
            [
                'prodotto_id' => 13,
                'titolo' => 'Controlli touch non funzionano',
                'descrizione' => 'Controlli touch non rispondono o rispondono male',
                'gravita' => 'critica',
                'soluzione' => "1. Spegnere e attendere 10 minuti\n2. Pulire superficie touch\n3. Verificare no pentole sui controlli\n4. Mani asciutte durante uso\n5. Verificare modulo elettronico\n6. Reset di fabbrica",
                'strumenti_necessari' => 'Detergente vetroceramica, panno microfibra',
                'tempo_stimato' => 30,
                'difficolta' => 'media',
                'numero_segnalazioni' => 6,
                'prima_segnalazione' => '2024-05-08',
                'ultima_segnalazione' => '2024-07-20',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI PIANO COTTURA GAS (ID: 14) ===
            [
                'prodotto_id' => 14,
                'titolo' => 'Fiamma gialla invece che blu',
                'descrizione' => 'Bruciatori producono fiamma gialla/arancione',
                'gravita' => 'alta',
                'soluzione' => "1. Pulire bruciatori e spartifiamma\n2. Verificare fori bruciatori\n3. Controllare pressione gas\n4. Posizionare spartifiamma correttamente\n5. Pulire fori uscita gas\n6. Controllare qualità aria",
                'strumenti_necessari' => 'Spazzolino metallico, ago sottile, sgrassante',
                'tempo_stimato' => 35,
                'difficolta' => 'media',
                'numero_segnalazioni' => 12,
                'prima_segnalazione' => '2024-03-15',
                'ultima_segnalazione' => '2024-07-19',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI CAPPA SilentPower (ID: 15) ===
            [
                'prodotto_id' => 15,
                'titolo' => 'Cappa poco efficace',
                'descrizione' => 'Non aspira sufficientemente fumi e odori',
                'gravita' => 'media',
                'soluzione' => "1. Pulire/sostituire filtri antigrasso\n2. Verificare ostruzioni condotto\n3. Controllare tenuta giunzioni\n4. Verificare velocità adeguata\n5. Controllare motore aspirante\n6. Verificare valvola antiriflusso",
                'strumenti_necessari' => 'Filtri nuovi, spazzola condotti, torcia',
                'tempo_stimato' => 30,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 24,
                'prima_segnalazione' => '2024-02-08',
                'ultima_segnalazione' => '2024-07-28',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI MICROONDE CombiSteam (ID: 17) ===
            [
                'prodotto_id' => 17,
                'titolo' => 'Riscaldamento non uniforme',
                'descrizione' => 'Alimenti riscaldati solo in alcune zone',
                'gravita' => 'media',
                'soluzione' => "1. Verificare rotazione piatto girevole\n2. Pulire guida piatto\n3. Controllare motore piatto\n4. Verificare antenna magnetron\n5. Pulire cavità interna\n6. Test con contenitore acqua",
                'strumenti_necessari' => 'Detergente microonde, panno umido, contenitore test',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 21,
                'prima_segnalazione' => '2024-01-12',
                'ultima_segnalazione' => '2024-07-29',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI CONDIZIONATORE ClimaPro (ID: 19) ===
            [
                'prodotto_id' => 19,
                'titolo' => 'Non raffredda sufficientemente',
                'descrizione' => 'Unità accesa ma non produce aria fredda adeguata',
                'gravita' => 'critica',
                'soluzione' => "1. Controllare/sostituire filtri aria\n2. Verificare unità esterna non ostruita\n3. Controllare livello gas refrigerante\n4. Verificare compressore\n5. Controllare alette condensatore\n6. ATTENZIONE: gas solo tecnico abilitato",
                'strumenti_necessari' => 'Filtri ricambio, spazzola, manometri gas (tecnico abilitato)',
                'tempo_stimato' => 60,
                'difficolta' => 'esperto',
                'numero_segnalazioni' => 11,
                'prima_segnalazione' => '2024-05-20',
                'ultima_segnalazione' => '2024-07-30',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI ROBOT ASPIRAPOLVERE (ID: 21) ===
            [
                'prodotto_id' => 21,
                'titolo' => 'Robot si blocca sempre stesso punto',
                'descrizione' => 'Si ferma ripetutamente in zona specifica',
                'gravita' => 'media',
                'soluzione' => "1. Controllare ostacoli invisibili\n2. Pulire sensori navigazione\n3. Verificare mappatura app\n4. Controllare altezza tappeti\n5. Reset mappa e rimappatura\n6. Verificare usura ruote",
                'strumenti_necessari' => 'Panno microfibra, app smartphone, metro',
                'tempo_stimato' => 25,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 18,
                'prima_segnalazione' => '2024-03-08',
                'ultima_segnalazione' => '2024-07-24',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI MACCHINA CAFFE (ID: 23) ===
            [
                'prodotto_id' => 23,
                'titolo' => 'Caffè troppo acquoso',
                'descrizione' => 'Caffè erogato risulta annacquato e poco corposo',
                'gravita' => 'bassa',
                'soluzione' => "1. Regolare macinatura più fine\n2. Aumentare dose caffè per tazza\n3. Controllare pressione pompa (15 bar)\n4. Pulire gruppo erogazione\n5. Controllare freschezza chicchi\n6. Decalcificare se acqua dura",
                'strumenti_necessari' => 'Decalcificante, chicchi freschi',
                'tempo_stimato' => 20,
                'difficolta' => 'facile',
                'numero_segnalazioni' => 31,
                'prima_segnalazione' => '2024-01-18',
                'ultima_segnalazione' => '2024-07-30',
                'creato_da' => 2
            ],

            // === MALFUNZIONAMENTI SCALDABAGNO (ID: 24) ===
            [
                'prodotto_id' => 24,
                'titolo' => 'Acqua non abbastanza calda',
                'descrizione' => 'Non raggiunge temperatura desiderata',
                'gravita' => 'alta',
                'soluzione' => "1. Verificare impostazione termostato (60°C)\n2. Controllare resistenza elettrica\n3. Verificare isolamento termico\n4. Controllare anodo magnesio\n5. Pulire resistenza da calcare\n6. Verificare capacità serbatoio",
                'strumenti_necessari' => 'Multimetro, chiavi resistenza, anodo ricambio',
                'tempo_stimato' => 80,
                'difficolta' => 'difficile',
                'numero_segnalazioni' => 9,
                'prima_segnalazione' => '2024-04-15',
                'ultima_segnalazione' => '2024-07-18',
                'creato_da' => 4
            ],

            // === MALFUNZIONAMENTI CALDAIA (ID: 25) ===
            [
                'prodotto_id' => 25,
                'titolo' => 'Caldaia va in blocco frequentemente',
                'descrizione' => 'Si blocca spesso con codice errore sul display',
                'gravita' => 'critica',
                'soluzione' => "SOLO TECNICO ABILITATO:\n1. Verificare pressione impianto (1-2 bar)\n2. Controllare tiraggio fumi\n3. Verificare elettrodo accensione\n4. Pulire scambiatore primario\n5. Controllare pompa circolazione\n6. Analisi fumi obbligatoria",
                'strumenti_necessari' => 'Analizzatore fumi, manometro, kit pulizia',
                'tempo_stimato' => 120,
                'difficolta' => 'esperto',
                'numero_segnalazioni' => 5,
                'prima_segnalazione' => '2024-05-28',
                'ultima_segnalazione' => '2024-07-15',
                'creato_da' => 4
            ]
        ];

        foreach ($malfunzionamenti as $malfunzionamento) {
            Malfunzionamento::create($malfunzionamento);
        }

        // === RIEPILOGO FINALE ===
        echo "\n============================================\n";
        echo "DATABASE POPOLATO CON SUCCESSO COMPLETO!\n";
        echo "============================================\n";
        
        echo "\nUTENTI CREATI (password: dNWRdNWR):\n";
        echo "- adminadmin (Mario Rossi - Amministratore)\n";
        echo "- staffstaff (Giulia Bianchi - Staff)\n"; 
        echo "- tecntecn (Francesco Verdi - Tecnico)\n";
        echo "- staff2 (Anna Neri - Staff)\n";
        echo "- tecnico2 (Luca Gialli - Tecnico)\n";
        echo "- tecnico3 (Sara Blu - Tecnico)\n";
        
        echo "\nCENTRI ASSISTENZA: " . count($centri) . "\n";
        echo "PRODOTTI CATALOGO: " . count($prodotti) . "\n";
        echo "MALFUNZIONAMENTI: " . count($malfunzionamenti) . "\n";
        
        echo "\nCATEGORIE PRODOTTI:\n";
        echo "- Lavatrici, Lavastoviglie, Frigoriferi\n";
        echo "- Forni, Asciugatrici, Piani Cottura\n";
        echo "- Cappe, Microonde, Condizionatori\n";
        echo "- Robot, Ferro Stiro, Macchina Caffè\n";
        echo "- Scaldabagni, Caldaie\n";
        
        echo "\nRICERCA WILDCARD SUPPORTATA:\n";
        echo "- 'lav*' -> lavatrici, lavastoviglie\n";
        echo "- 'frigo*' -> frigoriferi\n";
        echo "- 'forno*' -> forni\n";
        
        echo "\nCREDENZiali ACCESSO:\n";
        echo "URL: tweban.dii.univpm.it/~grp_51/laraProject/public\n";
        echo "User: adminadmin | staffstaff | tecntecn\n";
        echo "Pass: dNWRdNWR\n";
        
        echo "\nSISTEMA PRONTO PER IL TESTING!\n";
    }
}