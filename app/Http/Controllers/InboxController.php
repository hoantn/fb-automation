<?php

namespace App\Http\Controllers;

use App\Models\{Page, Customer, Conversation};
use App\Jobs\SendMessageJob;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Page $page)
    {
        $convs = Conversation::where('page_id',$page->id)
            ->orderByDesc('last_message_at')
            ->with('customer')
            ->paginate(20);

        return view('inbox.index', compact('page','convs'));
    }

    public function show(Page $page, Customer $customer)
    {
        abort_unless($customer->page_id === $page->id, 404);
        $messages = $customer->messages()->orderBy('id')->take(200)->get();
        return view('inbox.show', compact('page','customer','messages'));
    }

    public function send(Request $r, Page $page, Customer $customer)
    {
        $data = $r->validate(['text'=>'required|string|max:1000']);
        SendMessageJob::dispatch($page->id, $customer->id, $data['text'])->onQueue('fb-send');
        return back()->with('status','Đã đưa vào hàng đợi gửi.');
    }
}
