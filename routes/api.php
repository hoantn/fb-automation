<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

// Webhook Facebook: verify (GET) + events (POST)
Route::match(['GET', 'POST'], '/webhook/facebook', [WebhookController::class, 'handle'])
    ->name('webhook.facebook');
