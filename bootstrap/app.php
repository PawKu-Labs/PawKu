<?php

use App\Http\Middleware\EnsureMitraOwner;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureSupabaseAuthenticated;
use App\Http\Middleware\EnsureSupabaseGuest;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => EnsureSupabaseAuthenticated::class,
            'guest' => EnsureSupabaseGuest::class,
            'role' => EnsureRole::class,
            'superadmin' => EnsureSuperAdmin::class,
            'mitra.owner' => EnsureMitraOwner::class,
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // SEMENTARA: tampilkan error asli di browser. HAPUS setelah selesai debugging.
        $exceptions->render(function (\Throwable $e, Request $request) {
            // Biarkan validasi form tetap berjalan normal
            if ($e instanceof ValidationException) {
                return null;
            }

            return response(
                get_class($e) . "\n"
                . $e->getMessage() . "\n"
                . $e->getFile() . ':' . $e->getLine(),
                500
            )->header('Content-Type', 'text/plain');
        });
    })->create();