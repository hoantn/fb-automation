<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Broadcast, Page, Customer};
use App\Jobs\FacebookSendMessage;

class FacebookBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $broadcastId;
    public function __construct(int $broadcastId) { $this->broadcastId = $broadcastId; }

    public function handle(): void
    {
        $broadcast = Broadcast::find($this->broadcastId);
        if (!$broadcast) return;

        $page = Page::find($broadcast->page_id);
        if (!$page) return;

        $customerIds = Customer::where('page_id', $page->id)->pluck('id')->all();
        $broadcast->update(['total' => count($customerIds), 'status' => 'running']);

        $delay = 0;
        foreach ($customerIds as $cid) {
            FacebookSendMessage::dispatch($page->id, $cid, $broadcast->content)
                ->onQueue('fb-send')->delay(now()->addSeconds($delay));
            $delay += 1; // giãn cách an toàn
        }
    }
}
