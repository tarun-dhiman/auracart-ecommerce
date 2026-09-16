@extends('layouts.app')

@section('title', 'Secure Checkout — AuraCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    useSavedAddress: {{ $savedAddresses->isNotEmpty() ? 'true' : 'false' }},
    selectedAddressId: {{ $defaultAddress ? $defaultAddress->id : ($savedAddresses->first()->id ?? 'null') }},
    paymentMethod: 'cod'
}">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Express Checkout</h1>
        <p class="text-xs text-slate-500 mt-1">Provide your delivery destination and select payment method.</p>
    </div>

    <form method="POST" action="{{ route('checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        @csrf

        <!-- Left Columns (Form & Payment) -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Step 1: Shipping Address -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                        <h2 class="text-base font-bold text-slate-900">Shipping & Delivery Address</h2>
                    </div>
                    @if($savedAddresses->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <button type="button" @click="useSavedAddress = true" :class="useSavedAddress ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-xs transition">
                                Saved Addresses
                            </button>
                            <button type="button" @click="useSavedAddress = false" :class="!useSavedAddress ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg text-xs transition">
                                Enter New
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Saved Addresses Selector (if user has saved addresses) -->
                @if($savedAddresses->isNotEmpty())
                    <div x-show="useSavedAddress" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($savedAddresses as $addr)
                            <label class="relative p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between"
                                   :class="selectedAddressId === {{ $addr->id }} ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-start justify-between">
                                    <input type="radio" 
                                           name="saved_address_id" 
                                           value="{{ $addr->id }}"
                                           @click="selectedAddressId = {{ $addr->id }}"
                                           :checked="selectedAddressId === {{ $addr->id }}"
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    @if($addr->is_default)
                                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Default</span>
                                    @endif
                                </div>
                                <div class="mt-3 text-xs space-y-1 text-slate-600">
                                    <p class="font-bold text-slate-900">{{ $addr->full_name }}</p>
                                    <p>{{ $addr->phone }}</p>
                                    <p class="line-clamp-2">{{ $addr->formatted_address }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                <!-- Address Input Fields -->
                <div x-show="!useSavedAddress || {{ $savedAddresses->isEmpty() ? 'true' : 'false' }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name ?? '') }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Phone Number *</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" required placeholder="+91 98765 43210" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Address (for order tracking & invoice) *</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Flat / House No. / Building Name *</label>
                        <input type="text" name="address_line1" value="{{ old('address_line1') }}" required placeholder="e.g. Apartment 302, Prestige Towers" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Street / Area / Landmark (Optional)</label>
                        <input type="text" name="address_line2" value="{{ old('address_line2') }}" placeholder="e.g. 5th Main, Near City Park" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pincode *</label>
                            <input type="text" name="pincode" value="{{ old('pincode') }}" required placeholder="560001" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1">City *</label>
                            <input type="text" name="city" value="{{ old('city') }}" required placeholder="Bengaluru" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1">State *</label>
                            <input type="text" name="state" value="{{ old('state') }}" required placeholder="Karnataka" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Country *</label>
                            <input type="text" name="country" value="India" readonly class="w-full text-xs px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-600">
                        </div>
                    </div>

                    @auth
                        <div class="pt-2">
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="save_address" value="1" checked class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-300">
                                <span>Save this address to my profile for future orders</span>
                            </label>
                        </div>
                    @endauth
                </div>

                <!-- Customer Order Notes -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Delivery Instructions (Optional)</label>
                    <textarea name="customer_notes" rows="2" placeholder="e.g. Leave package at security or ring doorbell" class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none"></textarea>
                </div>
            </div>

            <!-- Step 2: Payment Method -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h2 class="text-base font-bold text-slate-900">Select Payment Method</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- Cash on Delivery -->
                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between"
                           :class="paymentMethod === 'cod' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between mb-3">
                            <input type="radio" name="payment_method" value="cod" @click="paymentMethod = 'cod'" :checked="paymentMethod === 'cod'" class="text-indigo-600 focus:ring-indigo-500">
                            <i data-lucide="banknote" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Cash on Delivery</h4>
                            <p class="text-[10px] text-slate-500 mt-0.5">Pay in cash when package arrives</p>
                        </div>
                    </label>

                    <!-- Razorpay -->
                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between"
                           :class="paymentMethod === 'razorpay' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between mb-3">
                            <input type="radio" name="payment_method" value="razorpay" @click="paymentMethod = 'razorpay'" :checked="paymentMethod === 'razorpay'" class="text-indigo-600 focus:ring-indigo-500">
                            <i data-lucide="credit-card" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Razorpay Gateway</h4>
                            <p class="text-[10px] text-slate-500 mt-0.5">UPI, Cards, Netbanking, Wallets</p>
                        </div>
                    </label>

                    <!-- Stripe -->
                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between"
                           :class="paymentMethod === 'stripe' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600' : 'border-slate-200 hover:border-slate-300'">
                        <div class="flex items-center justify-between mb-3">
                            <input type="radio" name="payment_method" value="stripe" @click="paymentMethod = 'stripe'" :checked="paymentMethod === 'stripe'" class="text-indigo-600 focus:ring-indigo-500">
                            <i data-lucide="globe" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Stripe Gateway</h4>
                            <p class="text-[10px] text-slate-500 mt-0.5">Visa, Mastercard, Amex Global</p>
                        </div>
                    </label>

                </div>
            </div>

        </div>

        <!-- Right Column: Order Summary & Placement -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Review Items ({{ $summary['items_count'] }})</h3>

                <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                    @foreach($summary['cart']->items as $item)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-xl border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $item->product->name }}</p>
                                <p class="text-[11px] text-slate-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            <span class="font-bold text-slate-900">₹{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-900">₹{{ number_format($summary['subtotal'], 2) }}</span>
                    </div>
                    @if($summary['discount'] > 0)
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>Coupon Discount</span>
                            <span>-₹{{ number_format($summary['discount'], 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span class="font-bold text-slate-900">{{ $summary['shipping'] == 0 ? 'FREE' : '₹' . number_format($summary['shipping'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>GST Tax (18%)</span>
                        <span class="font-bold text-slate-900">₹{{ number_format($summary['tax'], 2) }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                    <span class="text-sm font-bold text-slate-900">Grand Total</span>
                    <span class="text-2xl font-black text-slate-900">₹{{ number_format($summary['grand_total'], 2) }}</span>
                </div>

                <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-2xl transition duration-200 shadow-xl shadow-slate-200 flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Place Order Now</span>
                </button>

                <p class="text-[10px] text-slate-600 text-center">
                    By confirming, you authorize AuraCart to fulfill this order according to our terms and conditions.
                </p>
            </div>
        </div>

    </form>
</div>
@endsection
