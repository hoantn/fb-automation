<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Page;
use App\Models\Customer;

class FacebookService
{
    protected string $graphBase = 'https://graph.facebook.com/v17.0';

    public function verifySignature(string $payload, ?string $headerSignature): bool
    {
        $secret = env('FACEBOOK_APP_SECRET');
        if (!$secret) return true; // dev
        if (!$headerSignature || !str_starts_with($headerSignature, 'sha256=')) return false;

        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($hash, $headerSignature);
    }

    /** Lấy danh sách page user quản lý (dùng cho Connect Page) */
    public function listManagedPages(User $user): array
    {
        $token = $user->facebook_token ?? null;
        if (!\is_string($token) || $token === '') return [];

        $resp = Http::withToken($token)->get($this->graphBase . '/me/accounts', [
            'fields' => 'id,name,category,access_token',
        ]);

        if (!$resp->ok()) {
            Log::warning('FB list pages fail', ['status'=>$resp->status(), 'body'=>$resp->body()]);
            return [];
        }

        return $resp->json('data') ?: [];
    }

    /** Subscribe webhook events cho Page (messages, postbacks…) */
    public function subscribePageEvents(Page $page): bool
    {
        if (!$page->access_token || !$page->meta_page_id) return false;

        $resp = Http::asForm()
            ->withToken($page->access_token)
            ->post($this->graphBase . '/' . $page->meta_page_id . '/subscribed_apps', [
                'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins,message_deliveries,message_reads',
            ]);

        if (!$resp->ok()) {
            Log::warning('FB subscribe fail', ['page'=>$page->id, 'status'=>$resp->status(), 'body'=>$resp->body()]);
        }
        return $resp->ok();
    }

    /** Gửi message (dùng cho inbox & broadcast) */
    public function sendMessage(Page $page, Customer $customer, string $text): bool
    {
        if (!$page->access_token || !$customer->psid) return false;

        $resp = Http::withToken($page->access_token)->post($this->graphBase . '/me/messages', [
            'recipient' => ['id' => $customer->psid],
            'message'   => ['text' => $text],
            'messaging_type' => 'RESPONSE',
        ]);

        if (!$resp->ok()) {
            Log::warning('FB send message fail', ['status'=>$resp->status(), 'body'=>$resp->body()]);
        }

        return $resp->ok();
    }
}
