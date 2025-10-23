<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Page;
use App\Models\Customer;

/**
 * FacebookService
 *
 * Nguyên tắc:
 * - KHÔNG đổi interface đã dùng ở Step 2.
 * - Rõ ràng USER token vs PAGE token:
 *   + /me/accounts => USER token
 *   + /{page_id}/subscribed_apps, /me/messages => PAGE token
 * - Không throw exception ra controller; log đầy đủ để debug.
 */
class FacebookService
{
    /**
     * Kiến nghị pin version Graph API để tránh thay đổi đột ngột.
     * Có thể đưa vào config nếu cần.
     */
    protected string $graphBase = 'https://graph.facebook.com/v17.0';

    /**
     * Dùng USER token để liệt kê các Page mà user quản lý.
     * Trả về mảng các page gồm id, name, access_token (page).
     */
    public function listManagedPages(User $user): array
    {
        $userToken = $user->facebook_token ?? $user->token ?? null;
        if (!is_string($userToken) || $userToken === '') {
            Log::warning('listManagedPages: missing user token', ['user_id' => $user->id ?? null]);
            return [];
        }

        $resp = Http::withToken($userToken)
            ->get($this->graphBase . '/me/accounts', [
                'fields' => 'id,name,category,access_token',
            ]);

        if (!$resp->ok()) {
            Log::error('FB list pages fail', [
                'user_id' => $user->id ?? null,
                'status'  => $resp->status(),
                'body'    => $resp->body(),
            ]);
            return [];
        }

        $data = $resp->json('data') ?? [];
        return is_array($data) ? $data : [];
    }

    /**
     * Dùng PAGE token để đăng ký webhook events cho 1 Page.
     * Chỉ gọi sau khi đã lưu meta_page_id + access_token vào DB.
     */
    public function subscribePageEvents(Page $page): bool
    {
        $pageId = $page->meta_page_id ?? $page->page_id ?? null;
        $pageToken = $page->access_token ?? $page->token ?? null;

        if (!$pageId || !$pageToken) {
            Log::warning('subscribePageEvents: missing pageId or pageToken', [
                'page_id_db' => $page->id ?? null,
                'meta_page_id' => $page->meta_page_id ?? null,
                'access_token' => $page->access_token ? '***' : null,
            ]);
            return false;
        }

        $resp = Http::asForm()
            ->withToken($pageToken)
            ->post($this->graphBase . '/' . $pageId . '/subscribed_apps', [
                'subscribed_fields' => 'messages,messaging_postbacks,message_deliveries,message_reads,messaging_optins',
            ]);

        if (!$resp->ok()) {
            Log::error('FB subscribe fail', [
                'page_id_db'   => $page->id ?? null,
                'graph_page_id'=> $pageId,
                'status'       => $resp->status(),
                'body'         => $resp->body(),
            ]);
        }

        return $resp->ok();
    }

    /**
     * Gửi tin nhắn từ Page tới Customer (PSID) – dùng PAGE token.
     */
    public function sendMessage(Page $page, Customer $customer, string $text): bool
    {
        $pageToken = $page->access_token ?? $page->token ?? null;
        $psid = $customer->psid ?? null;

        if (!$pageToken || !$psid || $text === '') {
            Log::warning('sendMessage: missing token/psid/text', [
                'page_id_db' => $page->id ?? null,
                'psid'       => $psid,
                'has_text'   => $text !== '',
            ]);
            return false;
        }

        $resp = Http::withToken($pageToken)->post($this->graphBase . '/me/messages', [
            'recipient'      => ['id' => $psid],
            'message'        => ['text' => $text],
            'messaging_type' => 'RESPONSE',
        ]);

        if (!$resp->ok()) {
            Log::error('FB send message fail', [
                'page_id_db' => $page->id ?? null,
                'psid'       => $psid,
                'status'     => $resp->status(),
                'body'       => $resp->body(),
            ]);
        }

        return $resp->ok();
    }

    /**
     * Verify X-Hub-Signature-256 cho webhook (nếu bạn bật verify).
     * Dev có thể để FACEBOOK_APP_SECRET rỗng -> bỏ qua verify.
     */
    public function verifySignature(string $payload, ?string $headerSignature): bool
    {
        $secret = env('FACEBOOK_APP_SECRET');
        if (!$secret) return true; // Dev mode

        if (!$headerSignature || !str_starts_with($headerSignature, 'sha256=')) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $headerSignature);
    }
}
