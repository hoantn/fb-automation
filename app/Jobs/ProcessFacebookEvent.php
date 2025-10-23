<?php

namespace App\Jobs;

use App\Models\{Page, Customer, Conversation, Message};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ProcessFacebookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;
    public function __construct(array $payload) { $this->payload = $payload; }

    public function handle(): void
    {
        $entries = $this->payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $pageId = $entry['id'] ?? null;
            if (!$pageId) continue;

            $page = Page::where('meta_page_id', $pageId)->first();
            if (!$page) continue;

            foreach (($entry['messaging'] ?? []) as $m) {
                $psid = $m['sender']['id'] ?? null;
                $mid  = $m['message']['mid'] ?? null;
                $text = $m['message']['text'] ?? null;

                if (!$psid) continue;

                $customer = Customer::firstOrCreate(
                    ['page_id'=>$page->id, 'psid'=>$psid],
                    []
                );
                $customer->last_interaction_at = Carbon::now();
                $customer->save();

                $conv = Conversation::firstOrCreate(
                    ['page_id'=>$page->id, 'customer_id'=>$customer->id],
                    []
                );
                $conv->last_message_at = Carbon::now();

                if ($mid || $text) {
                    Message::create([
                        'page_id'=>$page->id,
                        'customer_id'=>$customer->id,
                        'direction'=>'in',
                        'mid'=>$mid,
                        'text'=>$text,
                        'status'=>'delivered',
                        'sent_at'=>Carbon::now(),
                        'raw'=>$m,
                    ]);
                }

                $conv->save();
            }
        }
    }
}
