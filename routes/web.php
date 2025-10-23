<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\PageConnectController;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/facebook/redirect', [FacebookAuthController::class,'redirect'])->name('fb.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class,'callback'])->name('fb.callback');

Route::middleware(['web','auth'])->group(function () {
    Route::get('/pages/connect', [PageConnectController::class,'listPages'])->name('pages.connect');
    Route::post('/pages/connect', [PageConnectController::class,'connect'])->name('pages.connect.post');
});

Route::get('/webhook/facebook', [WebhookController::class,'verify']);
Route::post('/webhook/facebook', [WebhookController::class,'handle']);
