@extends('layouts.app')

@section('title', 'All Collections — AuraCart')

@section('content')
<div class="bg-slate-100/50 py-8 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs text-slate-500 gap-2 mb-3">
            <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-slate-800 font-semibold">Catalog</span>
            @if(request('category'))
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-indigo-600 capitalize font-bold">{{ str_replace('-', ' ', request('category')) }}</span>
            @endif
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    @if(request('q'))
                        Search results for "{{ request('q') }}"
                    @elseif(request('category'))
                        {{ ucwords(str_replace('-', ' ', request('category'))) }}
                    @else
                        All Curated Products
                    @endif
                </h1>
                <p class="text-xs text-slate-500 mt-1">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} premium selections</p>
            </div>

            <!-- Sort Dropdown -->
            <div class="flex items-center gap-3">
                <label for="sort-select" class="text-xs font-bold text-slate-600 uppercase tracking-wider">Sort By:</label>
                <form id="sort-form" method="GET" action="{{ url()->current() }}">
                    <!-- Preserve existing query params except sort & page -->
                    @foreach(request()->except(['sort', 'page']) as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $subv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $subv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach

                    <select name="sort" id="sort-select" onchange="document.getElementById('sort-form').submit()" class="text-xs font-bold px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:border-indigo-500 text-slate-800">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Releases</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="bestseller" {{ request('sort') == 'bestseller' ? 'selected' : '' }}>Best Selling</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1 space-y-6">
            <form method="GET" action="{{ route('products.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                <!-- Search term preservation -->
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-indigo-600"></i>
                        <span>Filters</span>
                    </span>
                    <a href="{{ route('products.index') }}" class="text-xs text-rose-500 hover:text-rose-700 font-semibold">
                        Reset All
                    </a>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Categories</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                        @foreach($categories as $cat)
                            <label class="flex items-center justify-between text-xs text-slate-600 hover:text-indigo-600 cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="radio" 
                                           name="category" 
                                           value="{{ $cat->slug }}" 
                                           {{ request('category') === $cat->slug ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           class="text-indigo-600 focus:ring-indigo-500 rounded-full border-slate-300">
                                    <span class="{{ request('category') === $cat->slug ? 'font-bold text-indigo-600' : '' }}">{{ $cat->name }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full">{{ $cat->products_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Price Range (₹)</h4>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min ₹" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max ₹" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition">
                        Apply Price
                    </button>
                </div>

                <!-- Brands -->
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Brand</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                        @foreach($brands as $b)
                            @php $checked = is_array(request('brand')) ? in_array($b->slug, request('brand')) : request('brand') === $b->slug; @endphp
                            <label class="flex items-center justify-between text-xs text-slate-600 hover:text-indigo-600 cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" 
                                           name="brand[]" 
                                           value="{{ $b->slug }}" 
                                           {{ $checked ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-300">
                                    <span class="{{ $checked ? 'font-bold text-indigo-600' : '' }}">{{ $b->name }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full">{{ $b->products_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- In Stock Only Toggle -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') == '1' ? 'checked' : '' }} onchange="this.form.submit()" class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-300">
                        <span>In Stock Only</span>
                    </label>
                </div>
            </form>
        </aside>

        <!-- Product Grid -->
        <main class="lg:col-span-3">
            @if($products->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="package-search" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">No products match your criteria</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Try broadening your search term or adjusting filters to find what you desire.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                        <span>Reset All Filters</span>
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
</div>

<!-- Quick View Modal Container -->
@include('components.quickview-modal')

@endsection
