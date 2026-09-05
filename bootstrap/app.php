<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\DeleteStatusMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            Route::namespace('App\Http\Controllers')->group(function () {
                Route::prefix('api')->middleware(['api', 'maintenance'])->group(base_path('routes/api.php'));
                Route::middleware(['web'])->namespace('Admin')->prefix('royeladmin')->name('admin.')->group(base_path('routes/admin.php'));
                Route::middleware(['web', 'maintenance'])->prefix('user')->group(base_path('routes/user.php'));
                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth.basic'            => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'         => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                   => \Illuminate\Auth\Middleware\Authorize::class,
            'auth'                  => Authenticate::class,
            'guest'                 => RedirectIfAuthenticated::class,
            'password.confirm'      => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'                => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'              => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'              => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'admin'                 => RedirectIfNotAdmin::class,
            'admin.guest'           => RedirectIfAdmin::class,

            // 'check.status'          => CheckStatus::class,
            'delete.status'         => DeleteStatusMiddleware::class,

            'maintenance'           => MaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->respond(function ($response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => 'Unauthorized request'],
                    ]);
                }
            }
            return $response;
        });

    })->create();
