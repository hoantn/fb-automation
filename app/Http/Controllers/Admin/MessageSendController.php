<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Facebook\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class MessageSendController extends Controller
{
    public function __construct(private MessengerService $messenger) {}

    /**
     * POST /admin/inbox/reply
     * Body: page_id, psid, text, (optional) tag
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'page_id' => ['required', 'string'],
            'psid'    => ['required', 'string'],
            'text'    => ['required', 'string', 'max:1000'],
            'tag'     => ['nullable', 'string', Rule::in([
                // Chỉ dùng nếu bạn đã được phép: ví dụ HUMAN_AGENT qua Handover Protocol
                'HUMAN_AGENT','CONFIRMED_EVENT_UPDATE','POST_PURCHASE_UPDATE',
                'ACCOUNT_UPDATE','SHIPPING_UPDATE','RESERVATION_UPDATE'
            ])],
        ]);

        try {
            $resp = $this->messenger->sendText($data['page_id'], $data['psid'], $data['text'], $data['tag'] ?? null);
        } catch (RuntimeException $e) {
            // Trả JSON để UI hiển thị rõ lỗi
            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok'   => true,
            'data' => $resp,
            'msg'  => 'Sent',
        ]);
    }
}
