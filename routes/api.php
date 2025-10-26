<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

// Laravel 12: routes/api.php is auto-prefixed with /api via bootstrap/app.php
Route::get('/health', fn() => response()->json(['ok' => true]));

// Facebook Webhook
Route::get('/webhook/facebook',  [WebhookController::class, 'verify'])->name('webhook.facebook.verify');
Route::post('/webhook/facebook', [WebhookController::class, 'handle'])->name('webhook.facebook.handle');
