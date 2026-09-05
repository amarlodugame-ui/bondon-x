<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        $pageTitle = 'Admin Login';
        return view('admin.auth.login', compact('pageTitle'));
    }


    public function login(Request $request)
    {
        $admin = Admin::find(1);

        Auth::guard('admin')->login($admin);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
