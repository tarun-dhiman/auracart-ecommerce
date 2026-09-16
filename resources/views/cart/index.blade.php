@extends('layouts.app')

@section('title', 'Shopping Cart — AuraCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Shopping Bag</h1>
        <p class="text-xs text-slate-500 mt-1">Review your selected pieces before secure checkout.</p>
    </div>

    @if($summary['cart']->items->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm max-w-xl mx-auto my-8">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shopping-bag" class="w-8 h-8"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Your cart is currently empty</h2>
            <p class="text-xs text-slate-500 mb-6">Explore our curated collections of luxury electronics, fashion, and home lifestyle.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                <span>Discover Products</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">

            <!-- Cart Items Table (Left 2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Free Shipping Progress Indicator -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                    @php 
                        $threshold = $summary['free_shipping_threshold'];
                        $subtotal = $summary['subtotal'] - $summary['discount'];
                        $needed = max(0, $threshold - $subtotal);
                        $percent = min(100, round(($subtotal / $threshold) * 100));
                    @endphp
                    <div class="flex items-center justify-between text-xs">
                        @if($needed > 0)
                            <span class="font-medium text-slate-700">Add <strong class="text-indigo-600 font-bold">₹{{ number_format($needed, 2) }}</strong> more to unlock <strong class="text-emerald-600 font-bold">FREE Express Shipping</strong>!</span>
                        @else
                            <span class="font-bold text-emerald-600 flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> You have unlocked FREE Express Delivery!
                            </span>
                        @endif
                        <span class="font-bold text-slate-500 text-[10px]">{{ $percent }}%</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm divide-y divide-slate-100 overflow-hidden">
                    @foreach($summary['cart']->items as $item)
                        <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-4">
                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-2xl border border-slate-100 bg-slate-50">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-indigo-600 transition">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    @if($item->variant)
                                        <p class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $item->variant->variant_name }}</p>
                                    @endif
                                    <p class="text-xs font-black text-slate-900 mt-1">₹{{ number_format($item->price, 2) }}</p>
                                </div>
                            </div>

                            <!-- Quantity Selector Form & Subtotal -->
                            <div class="flex items-center justify-between w-full sm:w-auto sm:gap-6">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                                    @csrf
                                    <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-1.5 hover:bg-slate-200 text-slate-600 font-bold">-</button>
                                    <span class="w-10 text-center text-xs font-bold text-slate-900">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-1.5 hover:bg-slate-200 text-slate-600 font-bold">+</button>
                                </form>

                                <div class="text-right">
                                    <p class="text-sm font-black text-slate-900">₹{{ number_format($item->subtotal, 2) }}</p>
                                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-semibold text-rose-500 hover:text-rose-700 mt-1 flex items-center gap-0.5">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                            <span>Remove</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary (Right col) -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Coupon Input Form -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Promotional Code</h3>
                    @if($summary['coupon'])
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-emerald-800">Coupon applied: {{ $summary['coupon']->code }}</span>
                                <p class="text-[10px] text-emerald-600">Saved ₹{{ number_format($summary['discount'], 2) }}</p>
                            </div>
                            <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                @csrf
                                <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-bold">Remove</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" required placeholder="e.g. WELCOME10" class="flex-1 text-xs uppercase font-mono px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition">
                                Apply
                            </button>
                        </form>
                        <p class="text-[10px] text-slate-600">Try <span class="font-bold text-indigo-600">WELCOME10</span> for 10% off orders above ₹500.</p>
                    @endif
                </div>

                <!-- Cost Summary Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Order Summary</h3>

                    <div class="space-y-2.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal ({{ $summary['items_count'] }} items)</span>
                            <span class="font-bold text-slate-900">₹{{ number_format($summary['subtotal'], 2) }}</span>
                        </div>

                        @if($summary['discount'] > 0)
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Coupon Discount</span>
                                <span>-₹{{ number_format($summary['discount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span>Estimated Shipping</span>
                            @if($summary['shipping'] == 0)
                                <span class="font-bold text-emerald-600 uppercase text-[10px]">FREE</span>
                            @else
                                <span class="font-bold text-slate-900">₹{{ number_format($summary['shipping'], 2) }}</span>
                            @endif
                        </div>

                        <div class="flex justify-between">
                            <span>Estimated 18% GST</span>
                            <span class="font-bold text-slate-900">₹{{ number_format($summary['tax'], 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-slate-900">Grand Total</span>
                        <span class="text-2xl font-black text-slate-900">₹{{ number_format($summary['grand_total'], 2) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl transition shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                        <span>Proceed to Secure Checkout</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                    <div class="text-center pt-2 text-[10px] text-slate-600 flex items-center justify-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3 text-slate-600"></i>
                        <span>256-Bit SSL Encrypted Checkout</span>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
@endsection
