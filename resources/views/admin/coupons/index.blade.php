@extends('layouts.admin')

@section('title', 'Coupons & Promotions - AuraCart Admin')
@section('page_title', 'Coupons & Discounts')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Coupons List (Left 2 Cols) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Active Promo Codes</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Discounts applied during checkout by customers</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700">
                    {{ $coupons->total() }} Total Codes
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                            <th class="py-3 px-5">Code & Type</th>
                            <th class="py-3 px-5">Discount</th>
                            <th class="py-3 px-5">Rules / Limits</th>
                            <th class="py-3 px-5 text-center">Usages</th>
                            <th class="py-3 px-5 text-center">Status</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 tracking-wider text-xs">
                                            {{ $coupon->code }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 capitalize font-medium">
                                        {{ $coupon->type }} Discount
                                    </div>
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-800">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}% OFF
                                        @if($coupon->max_discount)
                                            <div class="text-[11px] font-normal text-slate-400">Up to ₹{{ number_format($coupon->max_discount) }}</div>
                                        @endif
                                    @else
                                        ₹{{ number_format($coupon->value) }} FLAT
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-xs text-slate-500">
                                    @if($coupon->min_order_value > 0)
                                        <div>Min: ₹{{ number_format($coupon->min_order_value) }}</div>
                                    @else
                                        <div>No min order</div>
                                    @endif
                                    @if($coupon->expiry_date)
                                        <div class="text-[11px] text-slate-400">Expires {{ $coupon->expiry_date->format('M d, Y') }}</div>
                                    @else
                                        <div class="text-[11px] text-emerald-600">No expiration</div>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="font-bold text-slate-700">{{ $coupon->usages_count }}</span>
                                    <span class="text-xs text-slate-400">/ {{ $coupon->usage_limit ?: '∞' }}</span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    @if($coupon->is_active && (!$coupon->expiry_date || $coupon->expiry_date->isFuture()))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Disabled
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="{{ $coupon->is_active ? 'Disable' : 'Enable' }}">
                                                <i data-lucide="{{ $coupon->is_active ? 'pause' : 'play' }}" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Coupon">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i data-lucide="ticket" class="w-10 h-10 mx-auto mb-2 text-slate-300 stroke-1"></i>
                                    <p class="text-sm font-semibold text-slate-600">No coupons available</p>
                                    <p class="text-xs text-slate-400 mt-1">Create your first coupon promo using the form on the right</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Coupon Form (Right 1 Col) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                <i data-lucide="plus-circle" class="w-5 h-5 text-amber-600"></i> New Promo Coupon
            </h2>
            <p class="text-xs text-slate-400 mb-6">Create promotional discount codes for marketing campaigns</p>

            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Coupon Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. FESTIVE20"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono uppercase font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Type *</label>
                        <select name="type" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed (₹)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Value *</label>
                        <input type="number" step="0.01" name="value" value="{{ old('value') }}" required placeholder="10 or 150"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Min Order (₹)</label>
                        <input type="number" step="0.01" name="min_order_value" value="{{ old('min_order_value', 0) }}"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Max Discount (₹)</label>
                        <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount') }}" placeholder="Optional cap"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Total Limit</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="Leave blank for ∞"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Per User Limit</label>
                        <input type="number" name="usage_per_user" value="{{ old('usage_per_user', 1) }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700">Activate immediately</label>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold rounded-xl text-sm shadow-md shadow-amber-900/10 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4"></i> Create Coupon
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
