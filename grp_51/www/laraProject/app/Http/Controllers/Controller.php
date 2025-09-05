<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Classe base Controller per tutti i controller dell'applicazione
 *
 * Fornisce i trait Laravel standard per autorizzazione, dispatch e validazione.
 * Tutti i controller personalizzati estendono questa classe.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}