@extends('customer.layout')

@section('title', 'My Orders — AuraCart')

@section('customer_content')
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">My Orders History</h2>
            <p class="text-xs text-slate-500">Track and manage your past and current consignments.</p>
        </div>

        <!-- Filter Pills -->
        <div class="flex items-center gap-1 overflow-x-auto text-[11px] font-semibold">
            <a href="{{ route('customer.orders') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">All</a>
            <a href="{{ route('customer.orders', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'pending' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Pending</a>
            <a href="{{ route('customer.orders', ['status' => 'shipped']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'shipped' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">In Transit</a>
            <a href="{{ route('customer.orders', ['status' => 'delivered']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'delivered' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Delivered</a>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 text-slate-500 text-xs">
            <i data-lucide="package-open" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p>No orders found matching this filter.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $ord)
                <div class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-200 transition space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-bold text-slate-900">{{ $ord->order_number }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-500">{{ $ord->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ord->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $ord->order_status)) }}
                            </span>
                            <span class="font-black text-slate-900">₹{{ number_format($ord->grand_total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Item Previews -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($ord->items as $item)
                            <div class="flex items-center gap-3 text-xs">
                                <img src="{{ $item->product->primary_image_url ?? '' }}" alt="" class="w-12 h-12 object-cover rounded-xl border border-slate-100">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ $item->product_name }}</p>
                                    <p class="text-[11px] text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <a href="{{ route('orders.invoice', $ord->order_number) }}" target="_blank" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition">
                            Invoice
                        </a>
                        <a href="{{ route('customer.orders.show', $ord->order_number) }}" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs transition">
                            View Details & Tracking
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
