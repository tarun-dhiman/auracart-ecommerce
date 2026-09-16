@extends('layouts.app')

@section('title', $product->name . ' — AuraCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    selectedImage: '{{ $product->primary_image_url }}',
    quantity: 1,
    selectedVariant: {{ $product->variants->isNotEmpty() ? $product->variants->first()->id : 'null' }},
    unitPrice: {{ $product->effective_price }},
    stockQty: {{ $product->stock_quantity }}
}">

    <!-- Breadcrumb -->
    <nav class="flex items-center text-xs text-slate-500 gap-2 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="hover:text-indigo-600 capitalize">
            {{ $product->category->name ?? 'Products' }}
        </a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Product Showcase Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">

        <!-- Left Column: Image Gallery -->
        <div class="space-y-4">
            <!-- Main Hero Image -->
            <div class="aspect-square bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm flex items-center justify-center relative group">
                <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">

                <!-- Discount Tag -->
                @if($product->discount_percentage > 0)
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-black bg-rose-500 text-white shadow-md">
                        -{{ $product->discount_percentage }}% OFF
                    </span>
                @endif

                <button type="button" 
                        onclick="toggleWishlist({{ $product->id }})"
                        class="absolute top-4 right-4 p-3 rounded-full bg-white/90 backdrop-blur-sm text-slate-500 hover:text-rose-500 hover:bg-white shadow-md transition"
                        title="Save to Wishlist">
                    <i data-lucide="heart" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Thumbnail Reel -->
            @if($product->images->count() > 1)
                <div class="flex items-center gap-3 overflow-x-auto custom-scrollbar pb-2">
                    @foreach($product->images as $img)
                        <button type="button" 
                                @click="selectedImage = '{{ $img->url }}'"
                                :class="selectedImage === '{{ $img->url }}' ? 'ring-2 ring-indigo-600 scale-95' : 'opacity-70 hover:opacity-100'"
                                class="w-20 h-20 rounded-2xl overflow-hidden bg-white border border-slate-200 flex-shrink-0 transition-all duration-200">
                            <img src="{{ $img->url }}" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Product Meta & Purchasing -->
        <div class="space-y-6 flex flex-col justify-between">
            <div>
                <!-- Brand & SKU -->
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    @if($product->brand)
                        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">{{ $product->brand->name }}</span>
                    @endif
                    <span class="font-mono text-[11px] text-slate-600">SKU: {{ $product->sku }}</span>
                </div>

                <!-- Product Name -->
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-3">
                    {{ $product->name }}
                </h1>

                <!-- Ratings & Reviews Summary -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'fill-amber-400 text-amber-400' : 'text-slate-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-sm font-bold text-slate-800">{{ $product->average_rating }}</span>
                    <span class="text-slate-300">|</span>
                    <a href="#reviews-section" class="text-xs font-semibold text-indigo-600 hover:underline">
                        {{ $product->reviews_count }} Verified Reviews
                    </a>
                </div>

                <!-- Price Block -->
                <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/80 mb-6">
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-black text-slate-900">
                            ₹{{ number_format($product->effective_price, 2) }}
                        </span>
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="text-base text-slate-600 line-through">
                                ₹{{ number_format($product->price, 2) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-500 text-white">
                                Save ₹{{ number_format($product->price - $product->sale_price, 2) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Inclusive of 18% GST and standard domestic handling taxes.</p>
                </div>

                <!-- Stock Status Indicator -->
                <div class="flex items-center gap-2 mb-6">
                    @if($product->stock_quantity > $product->low_stock_threshold)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-100">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> In Stock & Ready to Ship
                        </span>
                    @elseif($product->stock_quantity > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-amber-700 bg-amber-100">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Low Stock: Only {{ $product->stock_quantity }} Remaining
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-rose-700 bg-rose-100">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Short Description -->
                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                    {{ $product->short_description }}
                </p>

                <!-- Variants Selector (If available) -->
                @if($product->variants->isNotEmpty())
                    <div class="mb-6 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Select Option</label>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($product->variants as $v)
                                <button type="button"
                                        @click="selectedVariant = {{ $v->id }}; stockQty = {{ $v->stock_quantity }}; unitPrice = {{ $v->price ? $v->price : $product->effective_price }};"
                                        :class="selectedVariant === {{ $v->id }} ? 'border-indigo-600 bg-indigo-50/80 text-indigo-700 font-bold ring-2 ring-indigo-600' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'"
                                        class="px-4 py-2.5 rounded-xl border text-xs transition duration-200 flex items-center gap-2">
                                    <span>{{ $v->variant_name }}</span>
                                    @if($v->price)
                                        <span class="text-slate-600">₹{{ number_format($v->price, 2) }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quantity Selector & Actions -->
                @if($product->is_in_stock)
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm">
                                <button type="button" @click="if(quantity > 1) quantity--" class="px-4 py-3 text-slate-600 hover:bg-slate-100 font-black">-</button>
                                <input type="number" x-model.number="quantity" min="1" :max="stockQty" class="w-14 text-center font-bold text-sm bg-transparent border-0 focus:ring-0">
                                <button type="button" @click="if(quantity < stockQty) quantity++" class="px-4 py-3 text-slate-600 hover:bg-slate-100 font-black">+</button>
                            </div>

                            <button type="button" 
                                    @click="addToCart({{ $product->id }}, quantity, selectedVariant)"
                                    class="flex-1 py-3.5 px-6 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-sm rounded-2xl transition duration-200 shadow-lg shadow-slate-200 flex items-center justify-center gap-2">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                <span>Add to Cart</span>
                            </button>
                        </div>

                        <!-- Direct Checkout / Buy Now -->
                        <button type="button" 
                                @click="addToCart({{ $product->id }}, quantity, selectedVariant); window.location.href='{{ route('checkout.index') }}';"
                                class="w-full py-3.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-2xl transition duration-200 shadow-md shadow-indigo-100">
                            Buy Now with 1-Click
                        </button>
                    </div>
                @endif
            </div>

            <!-- Perks Strip -->
            <div class="grid grid-cols-3 gap-2 pt-6 border-t border-slate-100 text-center">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <i data-lucide="truck" class="w-4 h-4 text-indigo-600 mx-auto mb-1"></i>
                    <p class="text-[11px] font-bold text-slate-800">Priority Dispatch</p>
                    <p class="text-[9px] text-slate-500">Ships in 24 hrs</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 mx-auto mb-1"></i>
                    <p class="text-[11px] font-bold text-slate-800">Warranty</p>
                    <p class="text-[9px] text-slate-500">1-Year Official</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-amber-600 mx-auto mb-1"></i>
                    <p class="text-[11px] font-bold text-slate-800">Returns</p>
                    <p class="text-[9px] text-slate-500">7 Days Return</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Tabbed Detailed Information & Specifications -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm mb-16">
        <h2 class="text-xl font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Description & Product Details</h2>
        <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 mb-8">
            {!! nl2br(e($product->description)) !!}
        </div>

        @if($product->specifications && is_array($product->specifications))
            <h3 class="text-base font-bold text-slate-900 mb-4">Technical Specifications</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-100 rounded-2xl overflow-hidden">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($product->specifications as $label => $spec)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 font-bold text-slate-800 bg-slate-50/80 w-1/3">{{ $label }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ is_array($spec) ? implode(', ', $spec) : $spec }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Reviews & Ratings Section -->
    <section id="reviews-section" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm mb-16">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Customer Ratings & Reviews</h2>
                <p class="text-xs text-slate-500 mt-1">Real ratings from buyers who purchased this item</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-3xl font-black text-slate-900">{{ $product->average_rating }}</span>
                    <span class="text-xs text-slate-600">/ 5.0</span>
                </div>
                <div class="flex items-center text-amber-400">
                    @for($i = 1; $i <= 5; $i++)
                        <i data-lucide="star" class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'fill-amber-400 text-amber-400' : 'text-slate-300' }}"></i>
                    @endfor
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 pt-8">

            <!-- Submit Review Form -->
            <div class="lg:col-span-1 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Write a Review</h3>
                <p class="text-xs text-slate-500 mb-4">Share your experience to help fellow patrons.</p>

                @auth
                    <form method="POST" action="{{ route('customer.reviews.store', $product->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Rating</label>
                            <select name="rating" required class="w-full text-xs font-semibold px-3 py-2 bg-white border border-slate-200 rounded-xl focus:border-indigo-500">
                                <option value="5">★★★★★ (5 Stars - Exceptional)</option>
                                <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                                <option value="3">★★★☆☆ (3 Stars - Average)</option>
                                <option value="2">★★☆☆☆ (2 Stars - Below Expectation)</option>
                                <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Headline / Title</label>
                            <input type="text" name="title" placeholder="Summary of your review" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-xl focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Your Detailed Review</label>
                            <textarea name="comment" rows="4" required placeholder="What did you love about this item?" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-xl focus:border-indigo-500"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition">
                            Submit Review
                        </button>
                    </form>
                @else
                    <div class="text-center py-6">
                        <p class="text-xs text-slate-600 mb-3">Please sign in to write an authentic review.</p>
                        <a href="{{ route('login') }}" class="inline-flex px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Reviews List -->
            <div class="lg:col-span-2 space-y-4">
                @forelse($product->approvedReviews as $rev)
                    <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($rev->user->name ?? 'User', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $rev->user->name ?? 'Verified Collector' }}</h4>
                                    <p class="text-[10px] text-slate-600">{{ $rev->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if($rev->is_verified_purchase)
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <i data-lucide="check" class="w-3 h-3"></i> Verified Purchase
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center text-amber-400 gap-0.5">
                            @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-3.5 h-3.5 {{ $s <= $rev->rating ? 'fill-amber-400 text-amber-400' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>

                        @if($rev->title)
                            <h5 class="text-xs font-bold text-slate-900">{{ $rev->title }}</h5>
                        @endif
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $rev->comment }}</p>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-600">
                        <i data-lucide="message-square" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                        <p class="text-xs">No reviews submitted yet. Be the first to share your thoughts!</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section class="mb-16">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mb-6">Similar Products in {{ $product->category->name ?? 'Collection' }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
