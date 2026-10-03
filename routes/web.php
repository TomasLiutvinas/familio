<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// A read-only dependency probe for the Sern overview and deployment checks.
Route::get('/api/ready', function () {
    try {
        \Illuminate\Support\Facades\DB::selectOne('SELECT 1 AS ready');

        return response()->json([
            'status' => 'ready',
            'version' => config('app.release', 'development'),
        ]);
    } catch (\Throwable $exception) {
        // Keep database URLs, tokens and SQL diagnostics out of the response.
        return response()->json(['status' => 'unavailable'], 503);
    }
})->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
]);
