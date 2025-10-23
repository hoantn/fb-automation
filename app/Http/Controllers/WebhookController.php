<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function verify(Request $r)
    {
        if ($r->get('hub_mode') === 'subscribe' or $r->get('hub.mode') === 'subscribe') {
            return response($r->get('hub_challenge') ?? $r->get('hub.challenge'), 200);
        }
        return response('OK', 200);
    }

    public function handle(Request $r)
    {
        Log::info('FB webhook', ['payload' => $r->all()]);
        return response()->json(['ok' => true]);
    }
}
