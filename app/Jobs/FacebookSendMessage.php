<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Page, Customer, Message};
use App\Services\FacebookService;
use Illuminate\Support\Carbon;

class FacebookSendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $pageId;
    public int $customerId;
    public string $text;

    public function __construct(int $pageId, int $customerId, string $text)
    {
        $this->pageId = $pageId;
        $this->customerId = $customerId;
        $this->text = $text;
    }

    public function handle(FacebookService $fb): void
    {
        $page = Page::find($this->pageId);
        $customer = Customer::find($this->customerId);
        if (!$page || !$customer) return;

        $last = $customer->last_interaction_at;
        if ($last && now()->diffInHours($last) > 24) {
            Message::create([
                'page_id'=>$page->id,'customer_id'=>$customer->id,
                'direction'=>'out','text'=>$this->text,'status'=>'failed',
                'raw'=>['error'=>'outside_24h_window']
            ]);
            return;
        }

        $res = $fb->sendMessage($page, $customer->psid, $this->text);
        Message::create([
            'page_id'     => $page->id,
            'customer_id' => $customer->id,
            'direction'   => 'out',
            'text'        => $this->text,
            'status'      => $res['ok'] ? 'sent' : 'failed',
            'sent_at'     => $res['ok'] ? Carbon::now() : null,
            'raw'         => $res['body'] ?? null,
        ]);
    }
}
