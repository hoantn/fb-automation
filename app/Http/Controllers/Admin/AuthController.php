<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $r)
    {
        $data = $r->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $data['email'])->first();
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
        }

        $r->session()->put('admin_id', $admin->id);
        return redirect('/admin/dashboard');
    }

    public function logout(Request $r)
    {
        $r->session()->forget('admin_id');
        return redirect('/admin/login')->with('status', 'Đã đăng xuất');
    }
}
