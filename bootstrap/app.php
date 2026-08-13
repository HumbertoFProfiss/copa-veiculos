<?php

use App\Http\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(__DIR__.'/../routes/admin.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Roda antes de tudo (sessão, auth) - papéis/permissões e todo model
        // com BelongsToEmpresa dependem do tenant já estar resolvido.
        $middleware->web(prepend: [
            ResolveTenant::class,
        ]);

        // Guarda "cliente" tem login próprio - sem isso, um cliente deslogado
        // acessando /cliente/* cairia no login da equipe (guarda "web").
        $middleware->redirectGuestsTo(fn ($request) => $request->routeIs('cliente.*') ? route('cliente.login') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
