<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrazione del middleware personalizzato per controllo livelli
        $middleware->alias([
            'check.level' => \App\Http\Middleware\CheckUserLevel::class,
        ]);
        
        // Middleware globali (se necessari)
        // $middleware->append(\App\Http\Middleware\SomeGlobalMiddleware::class);
        
        // Middleware per gruppi specifici
        // $middleware->group('api', [
        //     \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Gestione personalizzata delle eccezioni (opzionale)
        // $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        //     if ($request->is('admin/*')) {
        //         return response()->view('admin.errors.404', [], 404);
        //     }
        // });
    })->create();