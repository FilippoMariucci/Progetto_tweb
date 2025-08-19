<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLevel
{
    /**
     * Middleware per verificare il livello di accesso dell'utente
     * Controlla che l'utente abbia almeno il livello minimo richiesto
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $minLevel  Livello minimo richiesto (2, 3, o 4)
     */
    public function handle(Request $request, Closure $next, ...$parameters): Response
    {
        // Il primo parametro è il livello minimo richiesto
        $minLevel = $parameters[0] ?? '1';

        // Verifica che l'utente sia autenticato
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autenticazione richiesta',
                    'redirect' => route('login')
                ], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Devi effettuare il login per accedere a questa pagina');
        }

        $user = Auth::user();
        $userLevel = (int) $user->livello_accesso;
        $requiredLevel = (int) $minLevel;

        // Verifica che l'utente abbia il livello minimo richiesto
        if ($userLevel < $requiredLevel) {
            $errorMessages = [
                2 => 'Accesso riservato ai tecnici e livelli superiori',
                3 => 'Accesso riservato allo staff aziendale e amministratori',
                4 => 'Accesso riservato agli amministratori',
            ];

            $message = $errorMessages[$requiredLevel] ?? 'Non hai i permessi necessari per accedere a questa risorsa';

            // Log del tentativo di accesso non autorizzato
            Log::warning('Tentativo accesso non autorizzato', [
                'user_id' => $user->id,
                'username' => $user->username,
                'user_level' => $userLevel,
                'required_level' => $requiredLevel,
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'is_ajax' => $request->ajax()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'user_level' => $userLevel,
                    'required_level' => $requiredLevel,
                    'redirect' => $this->getUserDashboardRoute($userLevel)
                ], 403);
            }

            if ($userLevel >= 2) {
                $dashboardRoute = $this->getUserDashboardRoute($userLevel);
                if (Route::has($dashboardRoute)) {
                    return redirect()->route($dashboardRoute)
                        ->with('error', $message);
                } else {
                    return redirect()->route('dashboard')
                        ->with('error', $message);
                }
            } else {
                $homeRoute = Route::has('home') ? 'home' : 'dashboard';
                return redirect()->route($homeRoute)->with('error', $message);
            }
        }

        // Log dell'accesso autorizzato (opzionale)
        Log::info('Accesso autorizzato', [
            'user_id' => $user->id,
            'username' => $user->username,
            'user_level' => $userLevel,
            'required_level' => $requiredLevel,
            'route' => $request->route()?->getName(),
            'is_ajax' => $request->ajax()
        ]);

        return $next($request);
    }

    /**
     * Ottiene la route della dashboard appropriata per il livello utente
     */
    private function getUserDashboardRoute(int $userLevel): string
    {
        return match ($userLevel) {
            4 => 'admin.dashboard',
            3 => 'staff.dashboard',
            2 => 'tecnico.dashboard',
            default => 'dashboard',
        };
    }
}
