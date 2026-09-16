@extends('layouts.admin')

@section('title', 'Edit Product: ' . $product->name . ' — AuraCart')
@section('header_title', 'Edit Product')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Edit: {{ $product->name }}</h1>
            <p class="text-xs text-slate-400">Updates saved here immediately sync across all storefront listings.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-white flex items-center gap-1">
            &larr; Back to Catalog
        </a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- General Information Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">General Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Product Title *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full text-xs font-mono px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Primary Category *</label>
                    <select name="category_id" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Sub-Category</label>
                    <select name="subcategory_id" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">None</option>
                        @foreach($categories as $cat)
                            @foreach($cat->subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('subcategory_id', $product->subcategory_id) == $sub->id ? 'selected' : '' }}>{{ $cat->name }} &rarr; {{ $sub->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Brand</label>
                    <select name="brand_id" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">None</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Short Description</label>
                <textarea name="short_description" rows="2" class="w-full text-xs px-3.5 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">{{ old('short_description', $product->short_description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Full Description</label>
                <textarea name="description" rows="5" class="w-full text-xs px-3.5 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <!-- Pricing & Inventory Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Pricing & Stock</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Regular Price (₹) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Sale Price (₹)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Low Stock Alert Threshold</label>
                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Media & Existing Images -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Product Images</h2>

            @if($product->images->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-4 mb-4">
                    @foreach($product->images as $img)
                        <div class="relative group aspect-square rounded-2xl overflow-hidden border border-slate-700 bg-slate-800">
                            <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover">
                            @if($img->is_primary)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-600 text-white">Primary</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="border-2 border-dashed border-slate-700 rounded-2xl p-6 text-center bg-slate-800/40">
                <p class="text-xs text-slate-400 mb-2">Upload additional imagery</p>
                <input type="file" name="images[]" multiple accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
            </div>
        </div>

        <!-- Visibility Flags -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Merchandising Status</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-300">
                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span class="font-bold text-white">Active (Live)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>Featured</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>Best Seller</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_new" value="1" {{ $product->is_new ? 'checked' : '' }} class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>New Arrival</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-900/50 transition">
                Save & Update Product
            </button>
        </div>

    </form>
</div>
@endsection
