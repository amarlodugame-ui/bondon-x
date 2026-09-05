<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $pageTitle = 'User Login';
        return view('theme.user.auth.login', compact('pageTitle'));
    }


    public function login(Request $request)
    {
         $user = User::find(1);
         Auth::login($user);
         return redirect()->intended(route('user.dashboard'));
    }




public function logout(Request $request)
{
    Auth::guard('user')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return to_route('user.login')->withNotify([['success', 'You have been logged out']]);
}


}
