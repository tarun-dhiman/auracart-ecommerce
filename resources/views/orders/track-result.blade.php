@extends('layouts.app')

@section('title', 'Consignment Status: ' . $order->order_number . ' — AuraCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-sm space-y-8">

        <!-- Tracking Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }} mb-1">
                    {{ strtoupper(str_replace('_', ' ', $order->order_status)) }}
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Consignment #{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Placed on {{ $order->created_at->format('M d, Y - h:i A') }}</p>
            </div>
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Invoice</span>
            </a>
        </div>

        <!-- 5-Stage Visual Progress Timeline -->
        @php
            $stages = [
                'pending' => 'Order Received',
                'confirmed' => 'Confirmed',
                'processing' => 'Dispatched / In Transit',
                'out_for_delivery' => 'Out for Delivery',
                'delivered' => 'Delivered'
            ];
            $orderKeys = array_keys($stages);
            $currentIdx = array_search($order->order_status, $orderKeys);
            if ($currentIdx === false && in_array($order->order_status, ['shipped'])) {
                $currentIdx = 2;
            } elseif ($order->order_status === 'delivered') {
                $currentIdx = 4;
            }
        @endphp

        <div class="py-6">
            <div class="relative flex items-center justify-between">
                <!-- Background Line -->
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 -z-0"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-600 transition-all duration-500 -z-0" style="width: {{ $currentIdx !== false ? ($currentIdx / (count($stages) - 1)) * 100 : 0 }}%"></div>

                @foreach($stages as $stKey => $stLabel)
                    @php 
                        $idx = array_search($stKey, $orderKeys);
                        $isPassed = $currentIdx !== false && $idx <= $currentIdx;
                        $isCurrent = $currentIdx !== false && $idx === $currentIdx;
                    @endphp
                    <div class="flex flex-col items-center relative z-10">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition shadow-md {{ $isPassed ? 'bg-indigo-600 text-white ring-4 ring-indigo-50' : 'bg-white border-2 border-slate-300 text-slate-400' }}">
                            @if($isPassed)
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @else
                                <span>{{ $loop->iteration }}</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-bold mt-2 text-center {{ $isCurrent ? 'text-indigo-600 font-extrabold' : 'text-slate-600' }}">
                            {{ $stLabel }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Carrier & Logistics Info -->
        @if($order->tracking_number)
            <div class="p-4 bg-indigo-50/60 rounded-2xl border border-indigo-100 flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-500">Carrier Partner:</span>
                    <strong class="text-slate-900 font-bold ml-1">{{ $order->carrier_name ?: 'Delhivery Express' }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Waybill / Tracking ID:</span>
                    <strong class="font-mono text-indigo-700 font-bold ml-1">{{ $order->tracking_number }}</strong>
                </div>
            </div>
        @endif

        <!-- Order Items -->
        <div>
            <h3 class="text-sm font-bold text-slate-900 mb-4">Package Contents</h3>
            <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                @foreach($order->items as $item)
                    <div class="p-4 flex items-center justify-between text-xs hover:bg-slate-50">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product->primary_image_url ?? '' }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded-xl border border-slate-100">
                            <div>
                                <p class="font-bold text-slate-900">{{ $item->product_name }}</p>
                                @if($item->variant_name)
                                    <p class="text-[11px] text-indigo-600 font-medium">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-[11px] text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                        </div>
                        <span class="font-bold text-slate-900">₹{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
