<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Page;
use App\Models\Customer;
use App\Models\PageToken;

class FacebookService
{
    protected string $graphBase = 'https://graph.facebook.com/v17.0';

    public function listManagedPages(User $user): array
    {
        $userToken = $user->facebook_token ?? $user->token ?? null;
        if (!is_string($userToken) || $userToken === '') {
            Log::warning('listManagedPages: missing user token', ['user_id' => $user->id ?? null]);
            return [];
        }

        $resp = Http::withToken($userToken)->get($this->graphBase . '/me/accounts', [
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

    public function ensurePageToken(
        Page $page,
        string $accessToken,
        ?array $scopes = null,
        ?int $issuedByUserId = null,
        ?string $status = 'active',
        $expiresAt = null
    ): bool {
        $ok = true;

        try {
            if (Schema::hasColumn('pages', 'access_token')) {
                if (!$page->access_token || $page->access_token !== $accessToken) {
                    $page->access_token = $accessToken;
                    $page->save();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ensurePageToken: cannot update pages.access_token', [
                'page_id_db' => $page->id ?? null,
                'err' => $e->getMessage(),
            ]);
            $ok = false;
        }

        try {
            if (Schema::hasTable('page_tokens')) {
                $payload = ['access_token' => $accessToken];
                if (Schema::hasColumn('page_tokens', 'scopes')) {
                    $payload['scopes'] = is_array($scopes) ? json_encode($scopes) : ($scopes ?? null);
                }
                if (Schema::hasColumn('page_tokens', 'issued_by_user_id')) {
                    $payload['issued_by_user_id'] = $issuedByUserId;
                }
                if (Schema::hasColumn('page_tokens', 'status')) {
                    $payload['status'] = $status;
                }
                if (Schema::hasColumn('page_tokens', 'expires_at')) {
                    $payload['expires_at'] = $expiresAt;
                }
                PageToken::updateOrCreate(['page_id' => $page->id], $payload);
            }
        } catch (\Throwable $e) {
            Log::warning('ensurePageToken: cannot upsert page_tokens', [
                'page_id_db' => $page->id ?? null,
                'err' => $e->getMessage(),
            ]);
            $ok = false;
        }

        return $ok;
    }

    public function subscribePageEvents(Page $page): bool
    {
        $pageId    = $page->meta_page_id ?? $page->page_id ?? null;
        $pageToken = $page->access_token ?? $page->token ?? null;

        if (!$pageId || !$pageToken) {
            Log::warning('subscribePageEvents: missing pageId or pageToken', [
                'page_id_db'   => $page->id ?? null,
                'meta_page_id' => $page->meta_page_id ?? null,
                'has_token'    => (bool)$pageToken,
            ]);
            return false;
        }

        $resp = Http::asForm()
            ->withToken($pageToken)
            ->post($this->graphBase . '/' . $pageId . '/subscribed_apps', [
                'subscribed_fields' => implode(',', [
                    'messages',
                    'messaging_postbacks',
                    'message_deliveries',
                    'message_reads',
                    'messaging_optins',
                ]),
            ]);

        if (!$resp->ok()) {
            Log::error('FB subscribe fail', [
                'page_id_db'    => $page->id ?? null,
                'graph_page_id' => $pageId,
                'status'        => $resp->status(),
                'body'          => $resp->body(),
            ]);
        }

        return $resp->ok();
    }

    public function sendMessage(Page $page, Customer $customer, string $text): bool
    {
        $pageToken = $page->access_token ?? $page->token ?? null;
        $psid      = $customer->psid ?? null;

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

    public function verifySignature(string $payload, ?string $headerSignature): bool
    {
        $secret = env('FACEBOOK_APP_SECRET');
        if (!$secret) return true;
        if (!$headerSignature || !str_starts_with($headerSignature, 'sha256=')) {
            return false;
        }
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $headerSignature);
    }
}
