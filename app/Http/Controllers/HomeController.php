<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (Auth::check() && $user && method_exists($user, 'pages')) {
            $pages = $user->pages()->orderBy('id', 'desc')->get();
        } else {
            $pages = Page::orderBy('id', 'desc')->get();
        }

        return view('home', compact('pages'));
    }
}
