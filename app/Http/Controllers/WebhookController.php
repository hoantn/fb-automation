<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // GET: verify webhook
        if ($request->isMethod('get')) {
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            Log::info('FB Webhook verify query', $request->query());

            if ($mode === 'subscribe' && $token === config('app.webhook_verify_token', env('WEBHOOK_VERIFY_TOKEN'))) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }
            return response('Invalid verify token', 403);
        }

        // POST: events
        Log::info('FB Webhook payload', ['payload' => $request->all()]);

        // Trả về ngay để Facebook nhận 200 (không bị retry)
        return response('EVENT_RECEIVED', 200);
    }
}
