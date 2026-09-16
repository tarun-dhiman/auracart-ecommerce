@extends('layouts.app')

@section('title', 'Processing Gateway Payment — AuraCart')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl space-y-6">

        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
            @if($payment_method === 'razorpay')
                <i data-lucide="credit-card" class="w-8 h-8"></i>
            @else
                <i data-lucide="globe" class="w-8 h-8"></i>
            @endif
        </div>

        <div>
            <span class="inline-block px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-2">
                {{ strtoupper($payment_method) }} Gateway
            </span>
            <h1 class="text-2xl font-black text-slate-900">Complete Your Payment</h1>
            <p class="text-xs text-slate-500 mt-1">Order #{{ $order->order_number }} for ₹{{ number_format($order->grand_total, 2) }}</p>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-left text-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Order ID:</span>
                <span class="font-bold text-slate-900">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Billed Amount:</span>
                <span class="font-black text-indigo-600 text-sm">₹{{ number_format($order->grand_total, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Recipient:</span>
                <span class="font-semibold text-slate-800">{{ $order->shipping_address['full_name'] ?? 'Customer' }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.verify', $order->order_number) }}">
            @csrf
            <input type="hidden" name="payment_method" value="{{ $payment_method }}">
            <input type="hidden" name="razorpay_payment_id" value="pay_sim_{{ uniqid() }}">
            <input type="hidden" name="razorpay_order_id" value="{{ $payload['razorpay_order_id'] ?? '' }}">
            <input type="hidden" name="payment_intent_id" value="pi_sim_{{ uniqid() }}">

            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl transition duration-200 shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-300"></i>
                <span>Simulate Successful {{ ucfirst($payment_method) }} Payment</span>
            </button>
        </form>

        <p class="text-[11px] text-slate-600">
            Payment keys configured in Admin Settings. In sandbox mode, clicking above confirms the payment instantly.
        </p>

    </div>
</div>
@endsection
