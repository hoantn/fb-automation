<?php

namespace App\Services;

use Illuminate\Support\Facades.Http;
use App\Models\{User, Page};

class FacebookService
{
    protected string $graphBase = 'https://graph.facebook.com/v17.0';

    public function listManagedPages(User $user): array
    {
        $token = $user->facebook_token ?? null;
        if (!$token) return [];
        $resp = Http::get($this->graphBase . '/me/accounts', [
            'access_token' => $token,
            'fields' => 'name,id,access_token,category'
        ]);
        if (!$resp->ok()) return [];
        $data = $resp->json();
        return $data['data'] ?? [];
    }

    public function subscribePageEvents(Page $page): bool
    {
        $token = optional($page->activeToken())->access_token;
        if (!$token) return false;

        $resp = Http::post($this->graphBase . "/{$page->meta_page_id}/subscribed_apps", [
            'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins,message_deliveries,message_reads',
            'access_token' => $token,
        ]);
        return $resp->ok();
    }

    public function sendMessage(Page $page, string $psid, string $text): array
    {
        $token = optional($page->activeToken())->access_token;
        if (!$token) return ['ok'=>false,'error'=>'no_token'];

        $resp = Http::post($this->graphBase . "/me/messages", [
            'recipient' => ['id' => $psid],
            'message'   => ['text' => $text],
            'messaging_type' => 'RESPONSE',
            'access_token' => $token,
        ]);

        return ['ok'=>$resp->ok(), 'body'=>$resp->json(), 'status'=>$resp->status()];
    }

    public function verifySignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = config('services.facebook.app_secret') ?? env('FACEBOOK_APP_SECRET');
        if (!$secret || !$signatureHeader) return false;
        if (!str_starts_with($signatureHeader, 'sha256=')) return false;

        $sig = substr($signatureHeader, 7);
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $sig);
    }
}
