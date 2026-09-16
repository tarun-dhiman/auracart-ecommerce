@extends('layouts.app')

@section('title', 'Track Order — AuraCart')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-sm space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-2">
                <i data-lucide="truck" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Track Your Consignment</h1>
            <p class="text-xs text-slate-500">Enter your order ID and the phone or email used during purchase.</p>
        </div>

        <form method="POST" action="{{ route('orders.track.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Order Number *</label>
                <input type="text" name="order_number" value="{{ request('order_number') }}" required placeholder="e.g. ORD-2026-000001" class="w-full text-xs font-mono uppercase px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email or Phone Number *</label>
                <input type="text" name="contact" required placeholder="Phone number or billing email" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition duration-200 shadow-md">
                Locate Package
            </button>
        </form>

        @auth
            <div class="text-center pt-2">
                <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    Or view full order history in your account &rarr;
                </a>
            </div>
        @endauth
    </div>
</div>
@endsection
