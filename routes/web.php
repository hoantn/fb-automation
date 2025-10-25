<?php

use Illuminate\Support\Facades\Route;

/**
 * ===== Controllers (khớp cấu trúc thường dùng trong repo của bạn) =====
 * Nếu tên/namespace khác, chỉnh lại cho đúng.
 */
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\PageConnectController;
use App\Http\Controllers\InboxController;
use App\Http\Middleware\VerifyCsrfToken;

// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\BroadcastController as AdminBroadcastController;

/*
|--------------------------------------------------------------------------
| Healthcheck / Utility
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json(['ok' => true]));

/*
|--------------------------------------------------------------------------
| Home (Dashboard người dùng)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
// Login route cho middleware auth
Route::get('/login', function () {
    return redirect('/auth/facebook/redirect');
})->name('login');

/*
|--------------------------------------------------------------------------
| Auth - Facebook SSO (Socialite)
|--------------------------------------------------------------------------
| - /auth/facebook/redirect: chuyển hướng đến FB
| - /auth/facebook/callback: nhận token user, tạo/cập nhật user
*/
Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirect'])
    ->name('fb.redirect');

Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback'])
    ->name('fb.callback');

/*
|--------------------------------------------------------------------------
| Pages Connect (liệt kê & kết nối Page)
|--------------------------------------------------------------------------
| GET  /pages/connect  -> listManagedPages() (gọi Graph bằng user token)
| POST /pages/connect  -> lưu Page + PageToken + auto-subscribe Page events
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/pages/connect',  [PageConnectController::class, 'listPages'])
        ->name('pages.connect');
    Route::post('/pages/connect', [PageConnectController::class, 'connect'])
        ->name('pages.connect.post');
});

/*
|--------------------------------------------------------------------------
| Inbox (xem hội thoại & gửi tin)
|--------------------------------------------------------------------------
| GET  /{page}/inbox         -> danh sách hội thoại của page
| POST /{page}/inbox/send    -> gửi tin nhắn (dùng page access token)
|
| {page} hỗ trợ Route Model Binding: App\Models\Page
*/
Route::middleware(['web', 'auth'])->group(function () {
    // Step 2 - Inbox
    Route::get('/{page}/inbox', [InboxController::class,'index'])->whereNumber('page');
    Route::get('/{page}/inbox/{customer}', [InboxController::class,'show'])->whereNumber(['page','customer']);
    Route::post('/{page}/inbox/{customer}/send', [InboxController::class,'send'])->whereNumber(['page','customer']);
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
| - /admin/dashboard
| - /admin/settings (GET/PUT)
| - các trang quản trị khác (pages/messages/broadcasts)
*/
Route::prefix('admin')->middleware(['web', 'auth'])->group(function () {
    // Dashboard tổng quan
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])
        ->name('admin.settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])
        ->name('admin.settings.update');

    // Tuỳ chọn: quản trị Page / Messages / Broadcasts (nếu có)
    Route::get('/pages', [AdminPageController::class, 'index'])
        ->name('admin.pages.index');
    Route::get('/messages', [AdminMessageController::class, 'index'])
        ->name('admin.messages.index');
    Route::get('/broadcasts', [AdminBroadcastController::class, 'index'])
        ->name('admin.broadcasts.index');
});
