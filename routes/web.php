<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\{WebhookController, InboxController, HomeController, FacebookAuthController, PageConnectController};
use App\Http\Controllers\Admin\{AuthController, DashboardController, PageController as AdminPageController, MessageController as AdminMessageController, BroadcastController as AdminBroadcastController, SettingController as AdminSettingController};
use App\Models\Page;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// Auth Facebook (Step 1)
Route::get('/auth/facebook/redirect', [FacebookAuthController::class,'redirect'])->name('fb.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class,'callback'])->name('fb.callback');

// Connect Page (Step 1)
Route::middleware(['web','auth'])->group(function () {
    Route::get('/pages/connect', [PageConnectController::class,'listPages'])->name('pages.connect');
    Route::post('/pages/connect', [PageConnectController::class,'connect'])->name('pages.connect.post');

    // Inbox (Step 2)
    Route::get('/{page}/inbox', [InboxController::class,'index'])->whereNumber('page');
    Route::get('/{page}/inbox/{customer}', [InboxController::class,'show'])->whereNumber(['page','customer']);
    Route::post('/{page}/inbox/{customer}/send', [InboxController::class,'send'])->whereNumber(['page','customer']);

    // Subscribe helper
    Route::post('/{page}/subscribe', function (Page $page) {
        $ok = app(\App\Services\FacebookService::class)->subscribePageEvents($page);
        return back()->with('status', $ok ? 'Subscribed!' : 'Subscribe failed');
    })->whereNumber('page');
});

// Webhook (Step 2)
Route::get('/webhook/facebook', [WebhookController::class,'verify']);
Route::post('/webhook/facebook', [WebhookController::class,'handle'])->withoutMiddleware([VerifyCsrfToken::class]);

// Admin (Step 3)
Route::get('/admin/login', [AuthController::class,'loginForm']);
Route::post('/admin/login', [AuthController::class,'login']);
Route::get('/admin/logout', [AuthController::class,'logout']);

Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index']);
    Route::get('/api/stats', [DashboardController::class,'stats']);

    Route::get('/pages', [AdminPageController::class,'index']);
    Route::get('/messages', [AdminMessageController::class,'index']);

    Route::get('/broadcasts', [AdminBroadcastController::class,'index']);
    Route::post('/broadcasts/send', [AdminBroadcastController::class,'send']);

    Route::get('/settings', [AdminSettingController::class,'index']);
    Route::post('/settings', [AdminSettingController::class,'update']);
});
