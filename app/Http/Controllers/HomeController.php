<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Nếu user đã đăng nhập và có quan hệ pages -> ưu tiên lấy theo user
        if (Auth::check() && $user && method_exists($user, 'pages')) {
            $pages = $user->pages()->orderBy('id', 'desc')->get();
        } else {
            // Chưa đăng nhập (hoặc chưa set quan hệ pages) -> fallback lấy toàn bộ pages hiện có
            $pages = Page::orderBy('id', 'desc')->get();
            // Nếu muốn ẩn khi guest, có thể thay bằng: $pages = collect();
        }

        return view('home', compact('pages'));
    }
}
