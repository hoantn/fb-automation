<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessFacebookEvent;

class WebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token     = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        if ($mode === 'subscribe' && $token === config('services.facebook.webhook_verify_token')) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }
        return response('Invalid verify token', 403);
    }

    public function handle(Request $request)
    {
        $rid = (string) Str::uuid();
        $payload = $request->json()->all() ?: $request->all();

        Log::info('webhook: hit', [
            'rid' => $rid, 'path' => $request->getPathInfo(),
            'entry_cnt' => is_array($payload) && isset($payload['entry']) ? count($payload['entry']) : null,
        ]);

        ProcessFacebookEvent::dispatch($payload, $rid);

        return response('EVENT_RECEIVED', 200);
    }
}
