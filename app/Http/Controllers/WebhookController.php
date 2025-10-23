<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessFacebookEvent;
use App\Services\FacebookService;

class WebhookController extends Controller
{
    public function verify(Request $r)
    {
        $token = env('WEBHOOK_VERIFY_TOKEN');
        if (($r->get('hub_mode') ?? $r->get('hub.mode')) === 'subscribe') {
            if (($r->get('hub_verify_token') ?? $r->get('hub.verify_token')) === $token) {
                return response($r->get('hub_challenge') ?? $r->get('hub.challenge'), 200);
            }
            return response('Invalid verify token', 403);
        }
        return response('OK', 200);
    }

    public function handle(Request $r, FacebookService $fb)
    {
        $signature = $r->header('X-Hub-Signature-256');
        $payload = $r->getContent();

        $secret = env('FACEBOOK_APP_SECRET');
        if ($secret && !$fb->verifySignature($payload, $signature)) {
            Log::warning('Invalid FB signature');
            return response()->json(['ok'=>false], 403);
        }

        $data = $r->all();
        ProcessFacebookEvent::dispatch($data)->onQueue('fb-webhook');
        return response()->json(['ok'=>true]);
    }
}
