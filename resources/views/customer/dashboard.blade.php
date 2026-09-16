@extends('customer.layout')

@section('title', 'My Account — AuraCart')

@section('customer_content')
<div class="space-y-6">

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium">Total Orders</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium">Pending Delivery</span>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ $pendingOrders }}</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium">Completed</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $completedOrders }}</p>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium">Wishlist Items</span>
            <p class="text-2xl font-black text-rose-500 mt-1">{{ $wishlistCount }}</p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Orders</h3>
                <p class="text-xs text-slate-500">Your latest purchases and their fulfillment progress</p>
            </div>
            <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                View All Orders &rarr;
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-10 text-slate-500 text-xs">
                <i data-lucide="shopping-cart" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p>You have not placed any orders yet.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl">
                    Explore Catalog
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-bold uppercase text-slate-400">
                            <th class="py-3">Order ID</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Total</th>
                            <th class="py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentOrders as $ord)
                            <tr>
                                <td class="py-3 font-mono font-bold text-slate-900">{{ $ord->order_number }}</td>
                                <td class="py-3 text-slate-500">{{ $ord->created_at->format('M d, Y') }}</td>
                                <td class="py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ord->status_badge_class }}">
                                        {{ ucfirst($ord->order_status) }}
                                    </span>
                                </td>
                                <td class="py-3 font-bold text-slate-900">₹{{ number_format($ord->grand_total, 2) }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('customer.orders.show', $ord->order_number) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
