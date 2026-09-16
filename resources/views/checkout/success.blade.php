@extends('layouts.app')

@section('title', 'Order Confirmed — AuraCart')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-xl space-y-6">

        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto shadow-inner">
            <i data-lucide="check-circle" class="w-10 h-10"></i>
        </div>

        <div>
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-2">
                Order Placed Successfully
            </span>
            <h1 class="text-2xl sm:text-4xl font-black text-slate-900">Thank You for Your Order!</h1>
            <p class="text-sm text-slate-500 mt-2">We have received your order and our logistics team is preparing your package.</p>
        </div>

        <!-- Order Number Badge -->
        <div class="inline-flex items-center gap-3 px-6 py-3 bg-slate-50 border border-slate-200 rounded-2xl">
            <span class="text-xs text-slate-500 font-medium">Order Number:</span>
            <span class="text-base font-mono font-black text-indigo-600">{{ $order->order_number }}</span>
        </div>

        <!-- Order Summary Card -->
        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-left text-xs space-y-3">
            <div class="flex justify-between pb-2 border-b border-slate-200/60 font-bold text-slate-900">
                <span>Payment Method</span>
                <span class="uppercase font-mono text-indigo-600">{{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Delivery Address:</span>
                <span class="font-semibold text-slate-800 text-right max-w-xs">
                    {{ $order->shipping_address['full_name'] ?? '' }}, {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['city'] ?? '' }} - {{ $order->shipping_address['pincode'] ?? '' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Grand Total:</span>
                <span class="text-sm font-black text-slate-900">₹{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Download / Print Invoice</span>
            </a>
            <a href="{{ route('orders.track') }}?order_number={{ $order->order_number }}" class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                <i data-lucide="truck" class="w-4 h-4"></i>
                <span>Track Delivery Status</span>
            </a>
        </div>

    </div>
</div>
@endsection
