@extends('layouts.admin')

@section('title', 'Products Management — AuraCart Control Hub')
@section('header_title', 'Product Catalog')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Search Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Products Catalog</h1>
            <p class="text-xs text-slate-400">Add, edit, toggle availability, and manage inventory.</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-900/40 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Product</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[200px] flex-1 max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or SKU..." class="w-full pl-9 pr-3 py-2 bg-slate-800 border border-slate-700 text-xs text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                <div class="absolute left-3 top-2.5 text-slate-500">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <select name="category" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 text-xs text-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 text-xs text-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Live)</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
            </select>

            <select name="stock" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 text-xs text-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                <option value="">All Stock</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock Alerts</option>
                <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </form>

        <span class="text-xs text-slate-500">Total: {{ $products->total() }} Products</span>
    </div>

    <!-- Products Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-800/60 border-b border-slate-800 text-[10px] font-bold uppercase text-slate-400">
                        <th class="py-3 px-6">Product</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Price</th>
                        <th class="py-3 px-4">Stock</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-800/40 transition">
                            <!-- Product Name & Thumbnail -->
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->primary_image_url }}" alt="" class="w-12 h-12 object-cover rounded-xl border border-slate-700 bg-slate-800">
                                    <div class="min-w-0 max-w-xs">
                                        <h4 class="font-bold text-white truncate">{{ $product->name }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            @if($product->is_featured)
                                                <span class="text-[9px] font-bold text-amber-400 bg-amber-400/10 px-1.5 py-0.5 rounded">Featured</span>
                                            @endif
                                            @if($product->is_bestseller)
                                                <span class="text-[9px] font-bold text-indigo-400 bg-indigo-400/10 px-1.5 py-0.5 rounded">Bestseller</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- SKU -->
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400">
                                {{ $product->sku }}
                            </td>

                            <!-- Category -->
                            <td class="py-3 px-4 text-slate-300">
                                {{ $product->category->name ?? 'None' }}
                            </td>

                            <!-- Price -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-white">₹{{ number_format($product->effective_price, 2) }}</span>
                                @if($product->sale_price)
                                    <span class="block text-[10px] text-slate-500 line-through">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                            </td>

                            <!-- Stock -->
                            <td class="py-3 px-4">
                                @if($product->stock_quantity <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400">Out of Stock</span>
                                @elseif($product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400">{{ $product->stock_quantity }} Low Stock</span>
                                @else
                                    <span class="text-slate-300 font-semibold">{{ $product->stock_quantity }} in stock</span>
                                @endif
                            </td>

                            <!-- Active Status Toggle Form -->
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('admin.products.toggle', $product->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-full text-[10px] font-bold transition {{ $product->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition" title="Preview on Website">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 text-indigo-400 hover:text-indigo-300 rounded-lg hover:bg-indigo-950/40 transition" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Delete this product permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-300 rounded-lg hover:bg-rose-950/40 transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                No products found in catalog. Click "Add New Product" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
