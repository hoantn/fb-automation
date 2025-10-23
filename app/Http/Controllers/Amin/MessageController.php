<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Message, Page};

class MessageController extends Controller
{
    public function index(Request $r)
    {
        $pageId = $r->integer('page_id');
        $pages  = Page::orderBy('name')->get();

        $q = Message::with(['customer','page'])->orderByDesc('id');
        if ($pageId) $q->where('page_id', $pageId);

        $messages = $q->paginate(25);
        return view('admin.messages', compact('messages','pages','pageId'));
    }
}
