<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Một endpoint cho cả GET (verify) và POST (event).
     * Route: Route::match(['GET','POST'], '/webhook/facebook', [WebhookController::class, 'handle']);
     */
    public function handle(Request $request)
    {
        // --- VERIFY (GET) ---
        if ($request->isMethod('get')) {
            // Facebook gửi các key dạng hub.mode / hub.verify_token / hub.challenge
            $mode       = $request->query('hub_mode') ?? $request->query('hub.mode');
            $token      = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
            $challenge  = $request->query('hub_challenge') ?? $request->query('hub.challenge');

            $expected = config('services.facebook.verify_token', env('WEBHOOK_VERIFY_TOKEN'));

            if ($mode === 'subscribe' && $token && $token === $expected) {
                return response($challenge, 200);
            }

            // Truy cập trực tiếp trên trình duyệt (không có hub.*) => trả text an toàn
            return response('Invalid verify token', 403);
        }

        // --- EVENT (POST) ---
        // (tùy chọn) log lại để debug
        Log::info('FB Webhook payload', ['headers' => $request->headers->all(), 'body' => $request->all()]);

        // Ở đây bạn có thể dispatch Job xử lý:
        // dispatch(new \App\Jobs\ProcessFacebookEvent($request->all()));

        // Facebook yêu cầu 200 trong 20s
        return response('EVENT_RECEIVED', 200);
    }
}
