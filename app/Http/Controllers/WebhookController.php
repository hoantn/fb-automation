<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * GET /webhook/facebook
     * Facebook verify webhook endpoint.
     */
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Token bạn đặt trong .env
        $verifyToken = config('services.facebook.webhook_verify_token', env('WEBHOOK_VERIFY_TOKEN'));

        if ($mode === 'subscribe' && $token && hash_equals($verifyToken ?? '', $token)) {
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Invalid verify token', 403);
    }

    /**
     * POST /webhook/facebook
     * Facebook gửi sự kiện (message, postback,…)
     */
    public function handle(Request $request)
    {
        // (Tùy chọn) kiểm tra chữ ký để bảo mật hơn
        $appSecret = config('services.facebook.app_secret', env('FACEBOOK_APP_SECRET'));
        $signature = $request->header('X-Hub-Signature-256');

        if ($appSecret && $signature) {
            // signature format: sha256=xxxx
            if (strpos($signature, '=') !== false) {
                [$algo, $hash] = explode('=', $signature, 2);
            } else {
                $algo = 'sha256';
                $hash = $signature;
            }
            $payload  = $request->getContent();
            $expected = hash_hmac('sha256', $payload, $appSecret);

            if (! hash_equals($expected, $hash)) {
                Log::warning('Webhook signature mismatch');
                return response('Invalid signature', 403);
            }
        }

        // Ghi log payload để theo dõi
        Log::info('FB Webhook payload', ['payload' => $request->all()]);

        // TODO: đẩy job ProcessFacebookEvent vào queue của bạn nếu cần
        // dispatch(new \App\Jobs\ProcessFacebookEvent($request->all()))->onQueue('fb-webhook');

        // Facebook yêu cầu trả 200 với text "EVENT_RECEIVED" hoặc "OK"
        return response('EVENT_RECEIVED', 200);
    }
}
