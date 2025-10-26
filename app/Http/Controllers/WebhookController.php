<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    /**
     * GET /api/webhook/facebook
     * Handles Facebook's verification (hub.challenge).
     */
    public function verify(Request $request)
    {
        // Facebook may send both styles with dot or underscore
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expected = config('services.facebook.webhook_verify_token');
        if ($mode === 'subscribe' && $token && $token === $expected) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('FB Webhook verify failed', [
            'mode' => $mode,
            // do not log token itself for security
        ]);
        return response('Invalid verify token', 403);
    }

    /**
     * POST /api/webhook/facebook
     * Receives events. Verifies X-Hub-Signature-256 if app secret is set.
     */
    public function handle(Request $request)
    {
        // Verify signature if provided (recommended by Meta)
        $appSecret = config('services.facebook.app_secret');
        $signature = $request->header('X-Hub-Signature-256');

        if ($appSecret && $signature) {
            $raw = $request->getContent() ?? '';
            $hash = 'sha256=' . hash_hmac('sha256', $raw, $appSecret);
            if (! hash_equals($hash, $signature)) {
                Log::warning('FB Webhook signature mismatch');
                return response('Invalid signature', Response::HTTP_FORBIDDEN);
            }
        }

        // Safely log the event type; avoid logging sensitive personal data wholesale
        $body = $request->json()->all() ?: $request->all();
        $object = is_array($body) && isset($body['object']) ? $body['object'] : null;

        Log::info('FB Webhook received', [
            'object' => $object,
            'has_entry' => is_array($body) && array_key_exists('entry', $body),
        ]);

        // TODO: Dispatch jobs/handlers per event type here if needed

        return response('EVENT_RECEIVED', 200);
    }
}
