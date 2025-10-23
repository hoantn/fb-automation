<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Broadcast, Page};
use App\Jobs\FacebookBroadcastJob;

class BroadcastController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('name')->get();
        $items = Broadcast::orderByDesc('id')->paginate(20);
        return view('admin.broadcasts', compact('pages','items'));
    }

    public function send(Request $r)
    {
        $data = $r->validate([
            'page_id' => 'required|integer|exists:pages,id',
            'content' => 'required|string|max:1000',
        ]);

        $b = Broadcast::create([
            'page_id' => $data['page_id'],
            'content' => $data['content'],
            'status'  => 'queued',
        ]);

        FacebookBroadcastJob::dispatch($b->id)->onQueue('broadcast');

        return back()->with('status', 'Đã thêm broadcast #' . $b->id);
    }
}
