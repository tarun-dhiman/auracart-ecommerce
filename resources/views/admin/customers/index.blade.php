@extends('layouts.admin')

@section('title', 'Customers Directory - AuraCart Admin')
@section('page_title', 'Customers Management')

@section('content')
<div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Registered Customers</h2>
            <p class="text-sm text-slate-500 mt-0.5">Manage customer accounts, verify contact info, order histories, and access status</p>
        </div>
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..."
                    class="pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 w-64 transition-all">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition-colors shadow-sm">
                Search
            </button>
            @if(request('q'))
                <a href="{{ route('admin.customers.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Clear filter">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Contact Info</th>
                        <th class="py-3.5 px-6 text-center">Orders Placed</th>
                        <th class="py-3.5 px-6">Registered</th>
                        <th class="py-3.5 px-6 text-center">Account Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-amber-700 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-slate-800 hover:text-amber-600 transition-colors">
                                            {{ $customer->name }}
                                        </a>
                                        <div class="text-xs text-slate-400">ID: #CUST-{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-slate-800 font-medium">{{ $customer->email }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $customer->phone ?: 'No phone provided' }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/50">
                                    {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $customer->created_at->format('M d, Y') }}
                                <div class="text-[11px] text-slate-400">{{ $customer->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($customer->is_blocked)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Blocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="View Customer Profile">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.customers.toggle-block', $customer->id) }}" onsubmit="return confirm('Are you sure you want to {{ $customer->is_blocked ? 'unblock' : 'block' }} this customer?')">
                                        @csrf
                                        <button type="submit" class="p-2 {{ $customer->is_blocked ? 'text-emerald-600 hover:bg-emerald-50' : 'text-rose-500 hover:bg-rose-50' }} rounded-lg transition-colors" title="{{ $customer->is_blocked ? 'Unblock Customer' : 'Block Customer' }}">
                                            <i data-lucide="{{ $customer->is_blocked ? 'shield-check' : 'shield-alert' }}" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 text-slate-300 stroke-1"></i>
                                <p class="text-base font-semibold text-slate-600">No customers found</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
