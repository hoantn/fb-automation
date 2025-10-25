<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

// GET: Facebook gọi để verify webhook (hub.challenge)
Route::get('/webhook/facebook', [WebhookController::class, 'verify'])
    ->name('webhook.facebook.verify');

// POST: Facebook push event (messages, postbacks…)
Route::post('/webhook/facebook', [WebhookController::class, 'handle'])
    ->name('webhook.facebook.handle');
