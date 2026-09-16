@extends('layouts.admin')

@section('title', 'Inventory & Stock Management — AuraCart Control Hub')
@section('header_title', 'Warehouse & Stock Control')

@section('content')
<div class="space-y-8" x-data="{ adjustModal: false, selectedProduct: null }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Inventory Management</h1>
            <p class="text-xs text-slate-400">Audit stock movement logs, perform manual restocks, and inspect low inventory.</p>
        </div>

        <!-- Filter tabs -->
        <div class="flex items-center gap-2 text-xs font-semibold">
            <a href="{{ route('admin.inventory.index') }}" class="px-3 py-1.5 rounded-xl {{ !request('stock_status') ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">All Stock</a>
            <a href="{{ route('admin.inventory.index', ['stock_status' => 'low']) }}" class="px-3 py-1.5 rounded-xl {{ request('stock_status') === 'low' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">Low Stock Alerts</a>
            <a href="{{ route('admin.inventory.index', ['stock_status' => 'out']) }}" class="px-3 py-1.5 rounded-xl {{ request('stock_status') === 'out' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">Out of Stock</a>
        </div>
    </div>

    <!-- Inventory Stock Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/60 border-b border-slate-800 text-[10px] font-bold uppercase text-slate-400">
                        <th class="py-3 px-6">Product</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Current Stock</th>
                        <th class="py-3 px-4">Threshold</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Stock Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $p->primary_image_url }}" alt="" class="w-10 h-10 object-cover rounded-xl border border-slate-700 bg-slate-800">
                                    <span class="font-bold text-white max-w-xs truncate">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400">{{ $p->sku }}</td>
                            <td class="py-3 px-4 font-bold text-sm text-white">{{ $p->stock_quantity }} units</td>
                            <td class="py-3 px-4 text-slate-400">{{ $p->low_stock_threshold }} units</td>
                            <td class="py-3 px-4">
                                @if($p->stock_quantity <= 0)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/30">Out of Stock</span>
                                @elseif($p->stock_quantity <= $p->low_stock_threshold)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Low Stock Alert</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Optimal</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-right">
                                <button type="button" 
                                        @click="selectedProduct = { id: {{ $p->id }}, name: '{{ addslashes($p->name) }}', stock: {{ $p->stock_quantity }} }; adjustModal = true;"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-xs transition">
                                    Adjust Stock
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Recent Inventory Audit Logs -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
        <div class="pb-3 border-b border-slate-800">
            <h3 class="text-sm font-bold text-white">Stock Movement Audit Trail (Live Logs)</h3>
            <p class="text-xs text-slate-400">Automatic order sales decrements, customer cancellations, and manual adjustments.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-[10px] font-bold uppercase text-slate-500">
                        <th class="py-2.5">Timestamp</th>
                        <th class="py-2.5">Product</th>
                        <th class="py-2.5">Movement Type</th>
                        <th class="py-2.5 text-center">Change</th>
                        <th class="py-2.5 text-center">Stock Level</th>
                        <th class="py-2.5">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="py-2.5 text-slate-400">{{ $log->created_at->format('d M H:i') }}</td>
                            <td class="py-2.5 font-semibold text-white">{{ $log->product->name ?? 'Unknown' }}</td>
                            <td class="py-2.5 uppercase font-mono text-[10px] text-slate-300">{{ str_replace('_', ' ', $log->type) }}</td>
                            <td class="py-2.5 text-center font-bold {{ $log->quantity_change > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $log->quantity_change > 0 ? '+' . $log->quantity_change : $log->quantity_change }}
                            </td>
                            <td class="py-2.5 text-center text-slate-400">{{ $log->quantity_before }} &rarr; <strong class="text-white">{{ $log->quantity_after }}</strong></td>
                            <td class="py-2.5 text-slate-400 italic">{{ $log->note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">No inventory movements recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div x-show="adjustModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="adjustModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-slate-900 border border-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-sm font-bold text-white">Adjust Stock Level</h3>
                    <button type="button" @click="adjustModal = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form :action="'/admin/inventory/' + (selectedProduct ? selectedProduct.id : 0) + '/adjust'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs text-slate-400">Target Product:</span>
                        <p class="text-sm font-bold text-white" x-text="selectedProduct ? selectedProduct.name : ''"></p>
                        <p class="text-xs text-slate-400 mt-0.5">Current Stock: <strong class="text-white" x-text="selectedProduct ? selectedProduct.stock : ''"></strong> units</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Stock Quantity Change (+/-) *</label>
                        <input type="number" name="quantity_change" required placeholder="e.g. +50 for restock, -5 for damage" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Adjustment Reason *</label>
                        <select name="type" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                            <option value="restock">Restock / Fresh Shipment</option>
                            <option value="manual_adjustment">Manual Audit Correction</option>
                            <option value="damage">Damaged / Expired / Written Off</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Audit Note (Optional)</label>
                        <input type="text" name="note" placeholder="e.g. Supplier Batch #941" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg transition">
                        Commit Stock Adjustment
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
