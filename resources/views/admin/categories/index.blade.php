@extends('layouts.admin')

@section('title', 'Categories Management — AuraCart Control Hub')
@section('header_title', 'Department & Category Hierarchy')

@section('content')
<div class="space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Categories & Subcategories</h1>
            <p class="text-xs text-slate-400">Organize your store catalog into intuitive navigation departments.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Forms Column (Left 1 col) -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Add Category Form -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white pb-2 border-b border-slate-800">Add Primary Category</h3>

                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Category Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Health & Personal Care" class="w-full text-xs px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Brief summary..." class="w-full text-xs px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Category Image</label>
                        <input type="file" name="image" accept="image/*" class="text-xs text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-slate-700 file:text-white">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" checked class="text-indigo-600 rounded bg-slate-800 border-slate-700">
                            <span>Featured on Homepage</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-900/40">
                        Create Category
                    </button>
                </form>
            </div>

            <!-- Add Subcategory Form -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white pb-2 border-b border-slate-800">Add Sub-Category</h3>

                <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Parent Category *</label>
                        <select name="category_id" required class="w-full text-xs px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                            <option value="">Choose Parent Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Subcategory Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Oral Care or Wooden Toothbrush" class="w-full text-xs px-3 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition">
                        Add Subcategory
                    </button>
                </form>
            </div>

        </div>

        <!-- Categories List (Right 2 cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-sm font-bold text-white pb-3 border-b border-slate-800">Current Catalog Categories ({{ $categories->count() }})</h3>

                <div class="space-y-4">
                    @foreach($categories as $cat)
                        <div class="p-5 bg-slate-800/50 rounded-2xl border border-slate-700/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $cat->image_url }}" alt="" class="w-12 h-12 object-cover rounded-xl border border-slate-700 bg-slate-800">
                                    <div>
                                        <h4 class="font-bold text-white text-sm">{{ $cat->name }}</h4>
                                        <p class="text-[11px] text-slate-400">{{ $cat->products->count() }} Linked Products</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete this category and its subcategories?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 p-2 rounded-lg hover:bg-slate-700 transition" title="Delete Category">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Subcategories Chips -->
                            @if($cat->subcategories->isNotEmpty())
                                <div class="pt-2 border-t border-slate-700/60">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1.5">Subcategories:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($cat->subcategories as $sub)
                                            <span class="px-2.5 py-1 rounded-lg text-xs bg-slate-800 text-slate-300 border border-slate-700">
                                                {{ $sub->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
