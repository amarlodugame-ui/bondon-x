<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        $pageTitle = 'User Login';

        $intendedPath = parse_url((string) $request->session()->get('url.intended', ''), PHP_URL_PATH) ?? '';

        return view('theme.checkout', ['pageTitle' => $pageTitle, 'quote' => null,
            'reviewLogin' => (bool) preg_match('~/product/[^/]+/review$~', $intendedPath)]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate(['identity' => ['required', 'string', 'max:191'], 'password' => ['required', 'string'], 'remember' => ['sometimes', 'boolean']]);
        $identity = trim($data['identity']);
        $key = 'purchase-login:'.hash('sha256', mb_strtolower($identity).'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['identity' => 'বারবার চেষ্টা হয়েছে। কিছুক্ষণ পর আবার লগইন করুন।']);
        }
        $column = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        if (! Auth::attempt([$column => $identity, 'password' => $data['password'], 'status' => 0, 'is_deleted' => 0], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['identity' => 'মোবাইল/ইমেইল অথবা পাসওয়ার্ড সঠিক নয়।']);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('checkout'))->with('success', 'Login successful');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('success', 'You have been logged out');
    }
}
