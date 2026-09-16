@extends('layouts.app')

@section('title', 'Sign In — AuraCart')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl space-y-6">

        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-2">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Welcome Back</h1>
            <p class="text-xs text-slate-500">Sign in to your AuraCart account to access your orders and wishlist.</p>
        </div>

        <!-- Quick Demo Credentials Fill Card -->
        <div class="p-4 bg-indigo-50/70 rounded-2xl border border-indigo-100 text-xs text-indigo-900 flex items-center justify-between">
            <div>
                <p class="font-bold">Demo Customer Account:</p>
                <p class="text-[11px] font-mono text-indigo-700">customer@auracart.com / password123</p>
            </div>
            <button type="button" 
                    onclick="document.getElementById('email').value='customer@auracart.com'; document.getElementById('password').value='password123';"
                    class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[10px] rounded-lg transition">
                Auto-fill
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                </div>
                <input type="password" id="password" name="password" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-300">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition duration-200 shadow-md">
                Sign In
            </button>
        </form>

        <div class="pt-2 text-center text-xs text-slate-500 border-t border-slate-100">
            Don't have an account yet? 
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">
                Create an account
            </a>
        </div>

    </div>
</div>
@endsection
