@extends('layouts.admin')

@section('title', 'Add New Product — AuraCart Control Hub')
@section('header_title', 'Create Product')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Add New Product</h1>
            <p class="text-xs text-slate-400">Newly added active products immediately display on the storefront.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-white flex items-center gap-1">
            &larr; Back to Catalog
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-2xl text-xs space-y-1">
            @foreach($errors->all() as $err)
                <p>• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- General Information Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">General Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Product Title *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Neem Wooden Toothbrush" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">SKU (Stock Keeping Unit)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Leave blank to auto-generate" class="w-full text-xs font-mono px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Primary Category *</label>
                    <select name="category_id" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Sub-Category</label>
                    <select name="subcategory_id" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">Select Subcategory (Optional)</option>
                        @foreach($categories as $cat)
                            @foreach($cat->subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('subcategory_id') == $sub->id ? 'selected' : '' }}>{{ $cat->name }} &rarr; {{ $sub->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Brand</label>
                    <select name="brand_id" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">Select Brand (Optional)</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Short Summary Description</label>
                <textarea name="short_description" rows="2" placeholder="One or two compelling sentences about the product..." class="w-full text-xs px-3.5 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">{{ old('short_description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Full Detailed Description</label>
                <textarea name="description" rows="5" placeholder="Comprehensive product background, features, and craft story..." class="w-full text-xs px-3.5 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Pricing & Inventory Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Pricing & Inventory</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Regular Price (₹) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required placeholder="199.00" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Sale Price (₹)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" placeholder="179.00" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Initial Stock *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 100) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Low Stock Alert Level</label>
                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" placeholder="0.25" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="e.g. 15 x 3 x 2 cm" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Tags (Comma-separated)</label>
                    <input type="text" name="tags" value="{{ old('tags') }}" placeholder="eco, oral care, neem" class="w-full text-xs px-3.5 py-2.5 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Product Images Upload -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Media & Photography</h2>
            <div class="border-2 border-dashed border-slate-700 rounded-2xl p-8 text-center bg-slate-800/40">
                <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-500 mx-auto mb-3"></i>
                <p class="text-xs font-bold text-white mb-1">Select multiple product images</p>
                <p class="text-[11px] text-slate-400 mb-4">PNG, JPG, JPEG, WEBP up to 5MB each. First uploaded image becomes primary.</p>
                <input type="file" name="images[]" multiple accept="image/*" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
            </div>
        </div>

        <!-- Visibility & Status Flags -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Status & Merchandising Flags</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-300">
                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_active" value="1" checked class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span class="font-bold text-white">Active (Published)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_featured" value="1" class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>Featured Item</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_bestseller" value="1" class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>Best Seller Tag</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer p-3 bg-slate-800/60 rounded-xl border border-slate-700/60">
                    <input type="checkbox" name="is_new" value="1" checked class="text-indigo-600 rounded bg-slate-700 border-slate-600">
                    <span>New Arrival Tag</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-900/50 transition">
                Save & Publish Product
            </button>
        </div>

    </form>
</div>
@endsection
