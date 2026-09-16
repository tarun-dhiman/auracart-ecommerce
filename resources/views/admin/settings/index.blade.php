@extends('layouts.admin')

@section('title', 'Store Settings - AuraCart Admin')
@section('page_title', 'Store Settings & Configuration')

@section('content')
<div class="max-w-5xl space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">System & Commerce Settings</h2>
            <p class="text-sm text-slate-500 mt-0.5">Configure store credentials, taxes, shipping rules, and payment gateways</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Production Ready
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf

        <!-- 1. General Store Identity -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <i data-lucide="store" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Store Profile & Branding</h3>
                    <p class="text-xs text-slate-400">Basic contact information shown on customer storefront, invoices, and emails</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Store Public Name</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Support Email</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Customer Support Phone</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">GSTIN / Tax ID Number</label>
                    <input type="text" name="store_gst" value="{{ old('store_gst', $settings['store_gst']) }}" placeholder="29ABCDE1234F1Z5"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Store Registered Address</label>
                    <textarea name="store_address" rows="2"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">{{ old('store_address', $settings['store_address']) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Currency, Tax & Shipping -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <i data-lucide="percent" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Financial, Tax & Shipping Rules</h3>
                    <p class="text-xs text-slate-400">Control currency symbols, GST calculation percentage, and delivery thresholds</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Default Tax Rate (%)</label>
                    <input type="number" step="0.1" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Standard Shipping (₹)</label>
                    <input type="number" step="0.1" name="shipping_fee" value="{{ old('shipping_fee', $settings['shipping_fee']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Free Shipping Over (₹)</label>
                    <input type="number" step="0.1" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>
            </div>
        </div>

        <!-- 3. Payment Gateways -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Payment Gateway Integrations</h3>
                    <p class="text-xs text-slate-400">Configure Cash on Delivery (COD), Razorpay (UPI/Cards/Netbanking), and Stripe</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Cash on Delivery -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-800 text-sm">Cash on Delivery (COD)</div>
                        <p class="text-xs text-slate-500 mt-0.5">Allow customers to pay via cash or UPI upon package delivery</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="enable_cod" value="0">
                        <input type="checkbox" name="enable_cod" value="1" {{ $settings['enable_cod'] == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>

                <!-- Razorpay -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-sky-700 tracking-tight text-base">Razorpay</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-100 text-sky-800">UPI / Cards / NetBanking</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enable_razorpay" value="0">
                            <input type="checkbox" name="enable_razorpay" value="1" {{ $settings['enable_razorpay'] == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Razorpay Key ID</label>
                            <input type="text" name="razorpay_key_id" value="{{ old('razorpay_key_id', $settings['razorpay_key_id']) }}" placeholder="rzp_test_..."
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Razorpay Key Secret</label>
                            <input type="password" name="razorpay_key_secret" value="{{ old('razorpay_key_secret', $settings['razorpay_key_secret']) }}"
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        </div>
                    </div>
                </div>

                <!-- Stripe -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-indigo-700 tracking-tight text-base">Stripe</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800">Global Credit & Debit Cards</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enable_stripe" value="0">
                            <input type="checkbox" name="enable_stripe" value="1" {{ $settings['enable_stripe'] == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Stripe Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" value="{{ old('stripe_publishable_key', $settings['stripe_publishable_key']) }}" placeholder="pk_test_..."
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Stripe Secret Key</label>
                            <input type="password" name="stripe_secret_key" value="{{ old('stripe_secret_key', $settings['stripe_secret_key']) }}"
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4 text-amber-400"></i> Save Store Configuration
            </button>
        </div>
    </form>
</div>
@endsection
