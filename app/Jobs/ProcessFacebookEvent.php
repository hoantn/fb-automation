<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessFacebookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $payload,
        private ?string $rid = null // optional trace id để bám log
    ) {}

    public function handle(): void
    {
        $entries = Arr::get($this->payload, 'entry', []);

        foreach ($entries as $entry) {
            // ID trang Facebook trả về trong webhook
            $pageUid   = (string) Arr::get($entry, 'id');

            // Chỉ cần nhánh messaging là đủ cho inbox
            foreach (Arr::get($entry, 'messaging', []) as $m) {
                $psid        = Arr::get($m, 'sender.id');               // PSID người dùng
                $mid         = Arr::get($m, 'message.mid');             // message id
                $text        = Arr::get($m, 'message.text');            // text
                $attachments = Arr::get($m, 'message.attachments');     // mảng attachments (nếu có)

                if (!$psid || (!$text && !$mid && empty($attachments))) {
                    continue; // không có nội dung hữu ích thì bỏ qua
                }

                $this->storeInbound($pageUid, $psid, $mid, $text, $attachments, $m);
            }
        }
    }

    private function storeInbound(string $metaPageId, string $psid, ?string $mid, ?string $text, $attachments, array $raw): void
    {
        // 1) Lấy page_id theo pages.meta_page_id
        $pageId = DB::table('pages')->where('meta_page_id', $metaPageId)->value('id');
        if (!$pageId) {
            Log::warning('webhook: page not found by meta_page_id', [
                'rid' => $this->rid, 'meta_page_id' => $metaPageId
            ]);
            return; // Không cố tạo page mới để tránh rác DB
        }

        DB::beginTransaction();
        try {
            // 2) Đảm bảo có customer theo PSID
            $customerId = DB::table('customers')->where('psid', $psid)->value('id');
            if (!$customerId) {
                $customerId = DB::table('customers')->insertGetId([
                    'psid'       => $psid,
                    'name'       => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3) Insert vào messages theo đúng schema
            $messageId = DB::table('messages')->insertGetId([
                'page_id'     => $pageId,
                'customer_id' => $customerId,
                'direction'   => 'in',
                'mid'         => $mid,
                'text'        => $text,
                'attachments' => is_array($attachments) ? json_encode($attachments, JSON_UNESCAPED_UNICODE) : $attachments,
                'status'      => 'received',
                'sent_at'     => Carbon::now(),
                'raw'         => json_encode($raw, JSON_UNESCAPED_UNICODE),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::commit();

            Log::info('webhook: inbound saved', [
                'rid'        => $this->rid,
                'meta_page_id' => $metaPageId,
                'page_id'    => $pageId,
                'psid'       => $psid,
                'mid'        => $mid,
                'text_len'   => $text ? mb_strlen($text) : 0,
                'message_id' => $messageId,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('webhook: inbound save failed', [
                'rid'   => $this->rid,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
