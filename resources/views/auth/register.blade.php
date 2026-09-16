@extends('layouts.app')

@section('title', 'Register Account — AuraCart')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl space-y-6">

        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-2">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Create Account</h1>
            <p class="text-xs text-slate-500">Join AuraCart for order tracking, wishlist sync, and member perks.</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-rose-50 rounded-xl text-xs text-rose-700 space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Mobile Phone (Optional)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password *</label>
                    <input type="password" id="password" name="password" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition duration-200 shadow-md">
                Create Account
            </button>
        </form>

        <div class="pt-2 text-center text-xs text-slate-500 border-t border-slate-100">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">
                Sign in
            </a>
        </div>

    </div>
</div>
@endsection
