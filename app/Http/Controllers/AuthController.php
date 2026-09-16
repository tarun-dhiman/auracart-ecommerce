<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if ($user->is_blocked) {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account has been deactivated. Please contact support.');
            }

            $request->session()->regenerate();
            $this->cartService->syncSessionCartToDatabase($user);

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('customer.dashboard'));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->with('error', 'The provided credentials do not match our records.');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $this->cartService->syncSessionCartToDatabase($user);

        return redirect()->route('customer.dashboard')->with('success', 'Account created successfully! Welcome to AuraCart.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}