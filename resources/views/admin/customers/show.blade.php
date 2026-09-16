@extends('layouts.admin')

@section('title', 'Customer Details: ' . $customer->name . ' - AuraCart Admin')
@section('page_title', 'Customer Profile')

@section('content')
<div class="space-y-6">
    <!-- Top Back & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-amber-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Customers Directory
        </a>
        <form method="POST" action="{{ route('admin.customers.toggle-block', $customer->id) }}" onsubmit="return confirm('Are you sure you want to {{ $customer->is_blocked ? 'unblock' : 'block' }} this customer?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm {{ $customer->is_blocked ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white' }}">
                <i data-lucide="{{ $customer->is_blocked ? 'shield-check' : 'shield-alert' }}" class="w-4 h-4"></i>
                {{ $customer->is_blocked ? 'Unblock Customer' : 'Block Customer Account' }}
            </button>
        </form>
    </div>

    <!-- Customer Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white font-black text-2xl flex items-center justify-center shadow-md">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ $customer->name }}</h2>
                        @if($customer->is_blocked)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Blocked
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-sm text-slate-500 mt-1">
                        <span class="flex items-center gap-1.5"><i data-lucide="mail" class="w-4 h-4 text-slate-400"></i> {{ $customer->email }}</span>
                        <span class="flex items-center gap-1.5"><i data-lucide="phone" class="w-4 h-4 text-slate-400"></i> {{ $customer->phone ?: 'No phone' }}</span>
                        <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i> Member since {{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Fast Metrics -->
            <div class="grid grid-cols-3 gap-4 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Spent</div>
                    <div class="text-xl font-black text-slate-900 mt-0.5">₹{{ number_format($totalSpent, 2) }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Orders</div>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ $customer->orders->count() }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Avg Order</div>
                    <div class="text-xl font-black text-slate-900 mt-0.5">
                        ₹{{ $customer->orders->count() > 0 ? number_format($totalSpent / $customer->orders->count(), 0) : '0' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Addresses -->
        <div class="pt-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-amber-600"></i> Saved Addresses ({{ $customer->addresses->count() }})
            </h3>
            @if($customer->addresses->isEmpty())
                <p class="text-sm text-slate-400 italic">No saved addresses on file.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($customer->addresses as $address)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 relative {{ $address->is_default ? 'ring-2 ring-amber-500/20 border-amber-300' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700">
                                    {{ $address->type ?? 'Address' }}
                                </span>
                                @if($address->is_default)
                                    <span class="text-[11px] font-bold text-amber-600 flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-500"></i> Default
                                    </span>
                                @endif
                            </div>
                            <div class="font-bold text-slate-800 text-sm">{{ $address->full_name }}</div>
                            <div class="text-xs text-slate-500 mt-1 leading-relaxed">
                                {{ $address->address_line1 }}<br>
                                @if($address->address_line2){{ $address->address_line2 }}<br>@endif
                                {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}<br>
                                {{ $address->country }}
                            </div>
                            <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                                <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i> {{ $address->phone }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-amber-600"></i> Order History
            </h3>
            <span class="text-xs font-semibold text-slate-500">{{ $customer->orders->count() }} total orders</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3 px-6">Order ID</th>
                        <th class="py-3 px-6">Date</th>
                        <th class="py-3 px-6">Items</th>
                        <th class="py-3 px-6">Total Amount</th>
                        <th class="py-3 px-6 text-center">Payment</th>
                        <th class="py-3 px-6 text-center">Fulfillment</th>
                        <th class="py-3 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($customer->orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="hover:text-amber-600">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $order->created_at->format('M d, Y, h:i A') }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $order->items->sum('quantity') }} items
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                ₹{{ number_format($order->grand_total, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Paid</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">{{ ucfirst($order->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    {{ $order->order_status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $order->order_status === 'shipped' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $order->order_status === 'processing' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $order->order_status === 'placed' ? 'bg-slate-100 text-slate-700' : '' }}
                                    {{ $order->order_status === 'cancelled' ? 'bg-rose-50 text-rose-700' : '' }}">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">
                                    Manage <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                This customer has not placed any orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
