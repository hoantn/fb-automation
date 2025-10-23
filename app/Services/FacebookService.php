<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;

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
}
