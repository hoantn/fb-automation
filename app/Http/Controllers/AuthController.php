<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FacebookAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->scopes([
            'public_profile','email','pages_show_list','pages_manage_metadata','pages_read_engagement','pages_messaging'
        ])->redirect();
    }

    public function callback()
    {
        $fb = Socialite::driver('facebook')->user();
        $user = User::updateOrCreate(
            ['facebook_user_id' => $fb->getId()],
            [
                'name'  => $fb->getName(),
                'email' => $fb->getEmail(),
                'avatar'=> $fb->avatar,
                'facebook_token' => $fb->token,
            ]
        );

        Auth::login($user);
        return redirect()->route('pages.connect');
    }
}
