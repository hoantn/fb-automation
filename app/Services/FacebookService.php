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

    // ... verifySignature(), listManagedPages() như đang dùng ...

    /** Kiểm tra page đã subscribe chưa */
    public function isPageSubscribed(Page $page): bool
    {
        if (!$page->access_token || !$page->meta_page_id) return false;

        $resp = Http::withToken($page->access_token)
            ->get($this->graphBase.'/'.$page->meta_page_id.'/subscribed_apps');

        if (!$resp->ok()) {
            Log::warning('FB check subscribed_apps fail', ['page'=>$page->id,'status'=>$resp->status(),'body'=>$resp->body()]);
            return false;
        }
        $data = $resp->json('data') ?: [];
        foreach ($data as $row) {
            // Nếu có entry thì coi như đã subscribe
            if (!empty($row['id'])) return true;
        }
        return false;
    }

    /** Gọi API subscribe */
    public function subscribePageEvents(Page $page): bool
    {
        if (!$page->access_token || !$page->meta_page_id) return false;

        $resp = Http::asForm()
            ->withToken($page->access_token)
            ->post($this->graphBase.'/'.$page->meta_page_id.'/subscribed_apps', [
                'subscribed_fields' => 'messages,messaging_postbacks,messaging_optins,message_deliveries,message_reads',
            ]);

        if (!$resp->ok()) {
            Log::warning('FB subscribe fail', ['page'=>$page->id,'status'=>$resp->status(),'body'=>$resp->body()]);
        }
        return $resp->ok();
    }

    /** Đảm bảo page đã subscribe (nếu chưa thì đăng ký) */
    public function ensurePageSubscription(Page $page): bool
    {
        if ($this->isPageSubscribed($page)) return true;
        return $this->subscribePageEvents($page);
    }

    /** Gửi message */
    public function sendMessage(Page $page, Customer $customer, string $text): bool
    {
        if (!$page->access_token || !$customer->psid) return false;

        $resp = Http::withToken($page->access_token)
            ->post($this->graphBase.'/me/messages', [
                'recipient'      => ['id' => $customer->psid],
                'message'        => ['text' => $text],
                'messaging_type' => 'RESPONSE',
            ]);

        if (!$resp->ok()) {
            Log::warning('FB send message fail', [
                'page'=>$page->id, 'psid'=>$customer->psid,
                'status'=>$resp->status(), 'body'=>$resp->body()
            ]);
        }
        return $resp->ok();
    }
}
