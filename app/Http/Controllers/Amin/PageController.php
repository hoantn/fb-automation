<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('id','desc')->get();
        return view('admin.pages', compact('pages'));
    }
}
