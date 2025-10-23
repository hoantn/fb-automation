<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\PageConnectController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\InboxController;
use App\Models\Page;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// Home (trang chủ test)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// Step 1 - SSO
Route::get('/auth/facebook/redirect', [FacebookAuthController::class,'redirect'])->name('fb.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class,'callback'])->name('fb.callback');

// Step 1 - Connect Page
Route::middleware(['web','auth'])->group(function () {
    Route::get('/pages/connect', [PageConnectController::class,'listPages'])->name('pages.connect');
    Route::post('/pages/connect', [PageConnectController::class,'connect'])->name('pages.connect.post');

    // Step 2 - Inbox
    Route::get('/{page}/inbox', [InboxController::class,'index'])->whereNumber('page');
    Route::get('/{page}/inbox/{customer}', [InboxController::class,'show'])->whereNumber(['page','customer']);
    Route::post('/{page}/inbox/{customer}/send', [InboxController::class,'send'])->whereNumber(['page','customer']);

    // Step 2 - helper: subscribe page events
    Route::post('/{page}/subscribe', function (Page $page) {
        $ok = app(\App\Services\FacebookService::class)->subscribePageEvents($page);
        return back()->with('status', $ok ? 'Subscribed!' : 'Subscribe failed');
    })->whereNumber('page');
});

// Step 2 - Webhook
Route::get('/webhook/facebook', [WebhookController::class,'verify']);
// TẮT CSRF cho webhook POST bằng withoutMiddleware:
Route::post('/webhook/facebook', [WebhookController::class,'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
