@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Account Navigation Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-6">
                <!-- User Profile Badge -->
                <div class="flex items-center gap-3 pb-6 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-black text-lg flex items-center justify-center shadow-md">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->name }}</h2>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('customer.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('customer.orders*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="{{ route('customer.wishlist') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('customer.wishlist') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                        <span>Saved Wishlist</span>
                    </a>
                    <a href="{{ route('customer.addresses') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('customer.addresses') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>Saved Addresses</span>
                    </a>
                    <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('customer.profile') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <span>Profile & Security</span>
                    </a>
                </nav>

                <div class="pt-4 border-t border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Customer Main Section Content -->
        <div class="lg:col-span-3">
            @yield('customer_content')
        </div>

    </div>
</div>
@endsection
