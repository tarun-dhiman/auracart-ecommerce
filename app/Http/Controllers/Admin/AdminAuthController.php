<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->regenerate();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if (!$user->isAdmin()) {
                Auth::logout();
                return redirect()->route('admin.login')->with('error', 'Access denied. You are not authorized as an administrator.');
            }

            if ($user->is_blocked) {
                Auth::logout();
                return redirect()->route('admin.login')->with('error', 'Your administrator account has been deactivated.');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back to AuraCart Control Hub!');
        }

        return redirect()->back()->withInput($request->only('email'))
            ->with('error', 'Invalid administrator credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Admin session ended securely.');
    }
}