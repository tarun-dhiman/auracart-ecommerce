@extends('customer.layout')

@section('title', 'Order ' . $order->order_number . ' — AuraCart')

@section('customer_content')
<div class="space-y-6">

    <!-- Order Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }} mb-1">
                    {{ strtoupper(str_replace('_', ' ', $order->order_status)) }}
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Order #{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-500">Placed on {{ $order->created_at->format('M d, Y - h:i A') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Invoice</span>
                </a>

                @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('customer.orders.cancel', $order->order_number) }}" onsubmit="return confirm('Are you sure you want to cancel this order? Stock will be restored.');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition">
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Tracking Bar -->
        @php
            $stages = ['pending' => 'Placed', 'confirmed' => 'Confirmed', 'processing' => 'Dispatched', 'shipped' => 'In Transit', 'delivered' => 'Delivered'];
            $keys = array_keys($stages);
            $cIdx = array_search($order->order_status, $keys);
            if ($cIdx === false && in_array($order->order_status, ['shipped'])) $cIdx = 3;
            if ($order->order_status === 'delivered') $cIdx = 4;
        @endphp

        @if(!in_array($order->order_status, ['cancelled', 'returned', 'refunded']))
            <div class="py-4">
                <div class="relative flex items-center justify-between">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 -z-0"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-600 -z-0" style="width: {{ $cIdx !== false ? ($cIdx / (count($stages) - 1)) * 100 : 0 }}%"></div>

                    @foreach($stages as $sk => $sl)
                        @php 
                            $idx = array_search($sk, $keys);
                            $passed = $cIdx !== false && $idx <= $cIdx;
                        @endphp
                        <div class="flex flex-col items-center relative z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $passed ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-300 text-slate-400' }}">
                                @if($passed)
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                @else
                                    <span>{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold mt-1 text-slate-700">{{ $sl }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-3 bg-rose-50 rounded-xl text-xs text-rose-700 font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>This order was {{ $order->order_status }}. Any inventory reserved has been restored.</span>
            </div>
        @endif

        @if($order->tracking_number)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-500">Carrier:</span>
                    <strong class="text-slate-900 font-bold ml-1">{{ $order->carrier_name ?: 'Delhivery Express' }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">AWB Tracking Number:</span>
                    <strong class="font-mono text-indigo-700 ml-1">{{ $order->tracking_number }}</strong>
                </div>
            </div>
        @endif
    </div>

    <!-- Items Table & Financial Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Order Items ({{ $order->items->count() }})</h3>
            <div class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product->primary_image_url ?? '' }}" alt="" class="w-14 h-14 object-cover rounded-xl border border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $item->product_name }}</h4>
                                @if($item->variant_name)
                                    <p class="text-[11px] text-indigo-600">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-[11px] text-slate-500">SKU: {{ $item->sku }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-900">₹{{ number_format($item->subtotal, 2) }}</p>
                            <p class="text-[11px] text-slate-500">{{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Cost Summary -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3 text-xs">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Financial Overview</h3>
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-medium">
                        <span>Discount:</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Shipping:</span>
                    <span>{{ $order->shipping_amount == 0 ? 'FREE' : '₹' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Tax (18% GST):</span>
                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 flex justify-between text-sm font-black text-slate-900">
                    <span>Total Billed:</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[11px]">
                    <span class="text-slate-500">Payment:</span>
                    <strong class="uppercase text-indigo-700 ml-1">{{ $order->payment_method }}</strong>
                    <span class="ml-1 text-slate-500">({{ ucfirst($order->payment_status) }})</span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 text-xs space-y-2">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Destination</h3>
                <p class="font-bold text-slate-900">{{ $order->shipping_address['full_name'] ?? '' }}</p>
                <p class="text-slate-600">{{ $order->shipping_address['phone'] ?? '' }}</p>
                <p class="text-slate-600 leading-relaxed">
                    {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['address_line2'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - {{ $order->shipping_address['pincode'] ?? '' }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
