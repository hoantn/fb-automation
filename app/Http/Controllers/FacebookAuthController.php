<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class FacebookAuthController extends Controller
{
    public function redirect()
    {
        // Cho local/dev: dùng stateless để không phụ thuộc session state
        return Socialite::driver('facebook')
            ->stateless()
            ->scopes([
                'public_profile',
                'email',
                'pages_show_list',
                'pages_manage_metadata',
                'pages_read_engagement',
                'pages_messaging',
            ])->redirect();
    }

    public function callback()
    {
        // Local/dev: stateless để tránh InvalidStateException
        $fb = Socialite::driver('facebook')->stateless()->user();

        $user = User::updateOrCreate(
            ['facebook_user_id' => $fb->getId()],
            [
                'name'           => $fb->getName(),
                'email'          => $fb->getEmail(),
                'avatar'         => $fb->avatar,
                'facebook_token' => $fb->token,
                // Patch cho cột password NOT NULL ở SQLite dev
                'password'       => Hash::make(Str::random(40)),
            ]
        );

        Auth::login($user);
        return redirect()->route('pages.connect');
    }
}
