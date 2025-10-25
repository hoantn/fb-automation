<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Facebook\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class InboxController extends Controller
{
    public function __construct(private MessengerService $messenger) {}

    /**
     * GET /admin/inbox
     * Shows conversation list. Expects table `conversations` with columns:
     * id, page_id, psid, name (nullable), last_user_message_at, updated_at
     */
    public function index(Request $request)
    {
        $threads = DB::table('conversations')
            ->select('id','page_id','psid','name','updated_at')
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        return view('admin.inbox.index', compact('threads'));
    }

    /**
     * GET /admin/inbox/{id}
     * Shows a conversation and its messages.
     * Expects table `messages` with columns:
     * id, conversation_id, direction (in|out), text, created_at
     */
    public function show(int $id)
    {
        $conversation = DB::table('conversations')->where('id', $id)->first();
        abort_unless($conversation, 404);

        $messages = DB::table('messages')
            ->where('conversation_id', $id)
            ->orderBy('id', 'asc')
            ->limit(500)
            ->get();

        return view('admin.inbox.show', compact('conversation', 'messages'));
    }

    /**
     * GET /admin/inbox/{id}/messages
     * JSON endpoint for polling (last 100 messages).
     */
    public function messagesJson(int $id)
    {
        $conversation = DB::table('conversations')->where('id', $id)->first();
        abort_unless($conversation, 404);

        $messages = DB::table('messages')
            ->where('conversation_id', $id)
            ->orderBy('id', 'asc')
            ->limit(200)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'direction' => $m->direction,
                    'text' => $m->text,
                    'created_at' => (string) $m->created_at,
                ];
            });

        return response()->json(['ok' => true, 'messages' => $messages]);
    }

    /**
     * POST /admin/inbox/{id}/reply
     * Validates and sends a reply to the PSID of this conversation.
     */
    public function reply(Request $request, int $id)
    {
        $data = $request->validate([
            'text' => ['required','string','max:1000'],
            'tag'  => ['nullable','string', Rule::in([
                'HUMAN_AGENT','CONFIRMED_EVENT_UPDATE','POST_PURCHASE_UPDATE',
                'ACCOUNT_UPDATE','SHIPPING_UPDATE','RESERVATION_UPDATE'
            ])],
        ]);

        $conversation = DB::table('conversations')->where('id', $id)->first();
        abort_unless($conversation, 404);

        try {
            $resp = $this->messenger->sendText($conversation->page_id, $conversation->psid, $data['text'], $data['tag'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withErrors(['reply' => $e->getMessage()])->withInput();
        }

        // persist outgoing message
        DB::table('messages')->insert([
            'conversation_id' => $conversation->id,
            'direction'       => 'out',
            'text'            => $data['text'],
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return back()->with('status', 'Đã gửi');
    }
}
