@extends('layouts.admin')

@section('title', 'Orders & Fulfillment — AuraCart Control Hub')
@section('header_title', 'Orders Management')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Order Management</h1>
            <p class="text-xs text-slate-400">Process incoming orders, track shipments, and oversee customer deliveries.</p>
        </div>
    </div>

    <!-- Status Filters & Search Toolbar -->
    <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[200px] flex-1 max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order ID, name, email..." class="w-full pl-9 pr-3 py-2 bg-slate-800 border border-slate-700 text-xs text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                <div class="absolute left-3 top-2.5 text-slate-500">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 text-xs text-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                <option value="">All Order Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped / In Transit</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 text-xs text-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                <option value="">All Payment Statuses</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </form>

        <span class="text-xs text-slate-500">Total: {{ $orders->total() }} Orders</span>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/60 border-b border-slate-800 text-[10px] font-bold uppercase text-slate-400">
                        <th class="py-3 px-6">Order ID</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Order Status</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-6 font-mono font-bold text-white">{{ $ord->order_number }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $ord->created_at->format('d M, H:i') }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-200">{{ $ord->shipping_address['full_name'] ?? 'Customer' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $ord->shipping_address['phone'] ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono uppercase text-[10px] text-slate-300 block">{{ $ord->payment_method }}</span>
                                <span class="text-[10px] font-bold {{ $ord->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ ucfirst($ord->payment_status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ord->status_badge_class }}">
                                    {{ ucfirst(str_replace('_', ' ', $ord->order_status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-white">₹{{ number_format($ord->grand_total, 2) }}</td>
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('orders.invoice', $ord->order_number) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition" title="Invoice">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.show', $ord->order_number) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-xs transition">
                                        Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No orders found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
