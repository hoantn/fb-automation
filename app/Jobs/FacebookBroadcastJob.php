<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Page, Customer, Broadcast};
use App\Jobs\FacebookSendMessage;
use Illuminate\Support\Facades\Bus;

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

        $customers = Customer::where('page_id', $page->id)->pluck('id')->toArray();
        $broadcast->total = count($customers);
        $broadcast->status = 'running';
        $broadcast->save();

        $batch = Bus::batch([])->dispatch();
        $delay = 0;
        foreach ($customers as $cid) {
            $job = (new FacebookSendMessage($page->id, $cid, $broadcast->content))
                ->onQueue('fb-send')
                ->delay(now()->addSeconds($delay));
            $batch->add($job);
            $delay += 1;
        }
    }
}
