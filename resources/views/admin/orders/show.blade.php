@extends('layouts.admin')

@section('title', 'Manage Order: ' . $order->order_number . ' — AuraCart')
@section('header_title', 'Order Fulfillment')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-white">Order #{{ $order->order_number }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->status_badge_class }}">
                    {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Placed on {{ $order->created_at->format('M d, Y - h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                &larr; Back to Orders
            </a>
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-md">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Invoice</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Left 2 Cols: Order Items & Customer Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Order Items -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
                <h3 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Order Items ({{ $order->items->count() }})</h3>

                <div class="divide-y divide-slate-800/60">
                    @foreach($order->items as $item)
                        <div class="py-4 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product->primary_image_url ?? '' }}" alt="" class="w-14 h-14 object-cover rounded-xl border border-slate-700 bg-slate-800">
                                <div>
                                    <h4 class="font-bold text-white text-sm">{{ $item->product_name }}</h4>
                                    @if($item->variant_name)
                                        <p class="text-xs text-indigo-400 font-semibold">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-[11px] font-mono text-slate-400 mt-0.5">SKU: {{ $item->sku }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-white">₹{{ number_format($item->subtotal, 2) }}</p>
                                <p class="text-[11px] text-slate-400">{{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Breakdown -->
                <div class="pt-4 border-t border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal:</span>
                        <span class="font-bold text-white">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-400">
                            <span>Coupon Discount ({{ $order->coupon_code }}):</span>
                            <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-400">
                        <span>Shipping Fee:</span>
                        <span>₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>GST Tax (18% Included):</span>
                        <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-800 flex justify-between text-base font-black text-white">
                        <span>Grand Total:</span>
                        <span>₹{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer & Addresses Information -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
                <h3 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Customer & Shipping Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-300">
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Customer Profile</span>
                        <p class="font-bold text-white text-sm">{{ $order->shipping_address['full_name'] ?? 'Guest Customer' }}</p>
                        <p class="text-slate-400">{{ $order->shipping_address['email'] ?? '' }}</p>
                        <p class="text-slate-400">{{ $order->shipping_address['phone'] ?? '' }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Shipping Destination</span>
                        <p class="leading-relaxed text-slate-300">
                            {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['address_line2'] ?? '' }}<br>
                            {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - {{ $order->shipping_address['pincode'] ?? '' }}<br>
                            {{ $order->shipping_address['country'] ?? 'India' }}
                        </p>
                    </div>
                </div>

                @if($order->customer_notes)
                    <div class="p-3 bg-slate-800/60 rounded-xl text-xs text-slate-300 mt-2">
                        <strong class="text-indigo-400 font-bold block mb-0.5">Customer Delivery Instructions:</strong>
                        {{ $order->customer_notes }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Status Updater & Fulfillment Controls -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
                <h3 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Fulfillment Controls</h3>

                <form method="POST" action="{{ route('admin.orders.status', $order->order_number) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Order Status</label>
                        <select name="order_status" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none font-bold">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped / In Transit</option>
                            <option value="out_for_delivery" {{ $order->order_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $order->order_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none font-bold">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Carrier Partner</label>
                        <input type="text" name="carrier_name" value="{{ old('carrier_name', $order->carrier_name) }}" placeholder="e.g. Delhivery Express, BlueDart" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tracking Number / AWB</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. DEL-98213829" class="w-full text-xs font-mono px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Admin Audit Notes</label>
                        <textarea name="admin_notes" rows="2" placeholder="Internal remarks..." class="w-full text-xs px-3.5 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-900/50 transition">
                        Update Order State
                    </button>
                </form>

                <p class="text-[10px] text-slate-500 leading-relaxed pt-2">
                    Cancelling or refunding an order automatically restores reserved units to warehouse inventory logs.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
