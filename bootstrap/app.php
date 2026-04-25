<?php

use App\Http\Middleware\AdminPermission;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            SetLocale::class,
        ]);

        // Trust Cloudflare (and any other proxy in front of the app) so
        // request()->ip() returns the real visitor IP and request()->isSecure()
        // honours the original HTTPS scheme. '*' is permissive — once the
        // Cloudflare front-door is the only path, switch this to the
        // pinned Cloudflare IP list at https://www.cloudflare.com/ips-v4
        $middleware->trustProxies(at: '*', headers:
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Register the per-permission gate so route definitions can use
        // ->middleware('admin.perm:plans.manage') etc.
        $middleware->alias([
            'admin.perm' => AdminPermission::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/paymob',
            // public/deploy.php is called by GitHub with an HMAC
            // signature, not a CSRF token — the path never actually
            // hits Laravel's middleware because it's a raw .php file,
            // but we list it here for clarity.
            'deploy.php',
        ]);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
