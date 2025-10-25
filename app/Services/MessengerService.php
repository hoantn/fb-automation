<?php

namespace App\Services\Facebook;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MessengerService
{
    /**
     * Get the latest active PAGE_ACCESS_TOKEN for a given page id.
     * Expects table `page_tokens` with columns: page_id, access_token, revoked_at (nullable), created_at.
     */
    protected function getActivePageToken(string $pageId): ?string
    {
        return DB::table('page_tokens')
            ->where('page_id', $pageId)
            ->whereNull('revoked_at')
            ->orderByDesc('created_at')
            ->value('access_token');
    }

    /**
     * Send a text message to a PSID using PAGE_ACCESS_TOKEN.
     * - Default messaging_type=RESPONSE (within 24h).
     * - If you pass $tag, it uses MESSAGE_TAG (requires permission).
     */
    public function sendText(string $pageId, string $psid, string $text, ?string $tag = null): array
    {
        $token = $this->getActivePageToken($pageId);
        if (!$token) {
            throw new RuntimeException("Missing PAGE_ACCESS_TOKEN for page_id={$pageId}");
        }

        $endpoint = 'https://graph.facebook.com/v20.0/me/messages?access_token='.$token;

        $payload = [
            'recipient' => ['id' => $psid],
            'message'   => ['text' => $text],
        ];

        if ($tag) {
            $payload['messaging_type'] = 'MESSAGE_TAG';
            $payload['tag'] = $tag;
        } else {
            $payload['messaging_type'] = 'RESPONSE';
        }

        $resp = Http::retry(2, 300)->asJson()->post($endpoint, $payload);

        if (!$resp->ok()) {
            $body = $resp->json() ?: ['raw' => $resp->body()];
            Log::error('FB send error', [
                'status' => $resp->status(),
                'resp'   => $body,
                'page'   => $pageId,
                'psid'   => $psid,
            ]);
            throw new RuntimeException('Facebook API error: '.json_encode($body));
        }

        return $resp->json();
    }
}
