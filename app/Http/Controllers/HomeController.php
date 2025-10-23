<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\{Page, Conversation};

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pages = collect();
        if ($user) {
            $pages = $user->pages()->get()->map(function (Page $p) {
                $inboxCount = Conversation::where('page_id', $p->id)->count();
                $lastConv   = Conversation::where('page_id', $p->id)->orderByDesc('last_message_at')->first();
                $lastAt     = optional($lastConv)->last_message_at;
                $hasToken   = (bool) optional($p->activeToken())->access_token;

                return [
                    'id'           => $p->id,
                    'name'         => $p->name,
                    'meta_page_id' => $p->meta_page_id,
                    'has_token'    => $hasToken,
                    'inbox_count'  => $inboxCount,
                    'last_at'      => $lastAt ? $lastAt->toDateTimeString() : null,
                ];
            });
        }

        $configHints = [
            'APP_URL'                 => config('app.url'),
            'FACEBOOK_CLIENT_ID'      => env('FACEBOOK_CLIENT_ID'),
            'FACEBOOK_REDIRECT_URI'   => env('FACEBOOK_REDIRECT_URI'),
            'FACEBOOK_APP_SECRET'     => env('FACEBOOK_APP_SECRET') ? '*** set ***' : 'MISSING',
            'WEBHOOK_VERIFY_TOKEN'    => env('WEBHOOK_VERIFY_TOKEN') ? '*** set ***' : 'MISSING',
            'Webhook URL (GET/POST)'  => url('/webhook/facebook'),
        ];

        return view('home', [
            'user'        => $user,
            'pages'       => $pages,
            'configHints' => $configHints,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('status', 'Đã đăng xuất.');
    }
}
