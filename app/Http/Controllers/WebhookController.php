<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        try {
            $signature = $r->header('X-Hub-Signature-256');
            $payload   = $r->getContent();

            $secret = env('FACEBOOK_APP_SECRET');
            if ($secret && !$fb->verifySignature($payload, $signature)) {
                Log::warning('FB webhook: invalid signature');
                return response()->json(['ok'=>false], 403);
            }

            $data = $r->all();

            // Chỉ dispatch nếu job tồn tại để tránh fatal -> 500
            if (class_exists(\App\Jobs\FacebookWebhookHandler::class)) {
                \App\Jobs\FacebookWebhookHandler::dispatch($data)->onQueue('fb-webhook');
            } elseif (class_exists(\App\Jobs\ProcessFacebookEvent::class)) {
                \App\Jobs\ProcessFacebookEvent::dispatch($data)->onQueue('fb-webhook');
            } else {
                Log::info('FB webhook received (no handler job found).', ['keys'=>array_keys($data ?? [])]);
            }

            return response()->json(['ok'=>true], 200);
        } catch (\Throwable $e) {
            Log::error('FB webhook error: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            // Trả 200 để Facebook không retry liên tục:
            return response()->json(['ok'=>false, 'note'=>'captured'], 200);
        }
    }
}
