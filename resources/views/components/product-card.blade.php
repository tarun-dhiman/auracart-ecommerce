@props(['product'])

<div class="group relative bg-white rounded-2xl border border-slate-200/80 hover:border-indigo-200 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300 flex flex-col overflow-hidden">
    <!-- Image & Badges Container -->
    <div class="relative aspect-square overflow-hidden bg-slate-100">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $product->primary_image_url }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy"
                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
        </a>

        <!-- Badges (Top Left) -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            @if($product->discount_percentage > 0)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-500 text-white shadow-sm tracking-wide">
                    -{{ $product->discount_percentage }}% OFF
                </span>
            @endif

            @if($product->is_new)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white shadow-sm tracking-wide">
                    NEW
                </span>
            @endif

            @if($product->stock_quantity <= 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-white shadow-sm">
                    OUT OF STOCK
                </span>
            @elseif($product->stock_quantity <= $product->low_stock_threshold)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-sm">
                    ONLY {{ $product->stock_quantity }} LEFT
                </span>
            @endif
        </div>

        <!-- Wishlist Button (Top Right) -->
        <button type="button" 
                onclick="toggleWishlist({{ $product->id }})"
                class="absolute top-3 right-3 p-2 rounded-full bg-white/90 backdrop-blur-sm text-slate-400 hover:text-rose-500 hover:bg-white shadow-sm hover:shadow-md transition-all duration-200 z-10"
                title="Save to Wishlist">
            <i data-lucide="heart" class="w-4 h-4"></i>
        </button>

        <!-- Quick View Overlay Button (Hover on Desktop) -->
        <div class="absolute inset-x-3 bottom-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hidden sm:block z-10">
            <button type="button" 
                    onclick="openQuickView({{ $product->id }})"
                    class="w-full py-2 px-3 bg-white/95 backdrop-blur-md hover:bg-indigo-600 hover:text-white text-slate-800 text-xs font-bold rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-1.5">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Quick View</span>
            </button>
        </div>
    </div>

    <!-- Product Details Content -->
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between">
        <div>
            <!-- Category & Brand -->
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                <span class="font-medium text-slate-500 uppercase tracking-wider text-[10px]">{{ $product->category->name ?? 'Collection' }}</span>
                @if($product->brand)
                    <span class="text-[10px] font-bold text-indigo-600">{{ $product->brand->name }}</span>
                @endif
            </div>

            <!-- Title -->
            <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 leading-snug mb-1">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating Stars -->
            <div class="flex items-center gap-1.5 mb-3">
                <div class="flex items-center text-amber-400">
                    @for($i = 1; $i <= 5; $i++)
                        <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= round($product->average_rating) ? 'fill-amber-400 text-amber-400' : 'text-slate-300' }}"></i>
                    @endfor
                </div>
                <span class="text-xs font-semibold text-slate-600">{{ $product->average_rating }}</span>
                <span class="text-[11px] text-slate-500">({{ $product->reviews_count }})</span>
            </div>
        </div>

        <!-- Price & Add to Cart -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-base sm:text-lg font-black text-slate-900">
                        ₹{{ number_format($product->effective_price, 2) }}
                    </span>
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-xs text-slate-600 line-through">
                            ₹{{ number_format($product->price, 2) }}
                        </span>
                    @endif
                </div>
            </div>

            @if($product->is_in_stock)
                <button type="button" 
                        onclick="addToCart({{ $product->id }}, 1)"
                        class="p-2.5 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl transition duration-200 shadow-sm hover:shadow-indigo-200 flex-shrink-0"
                        title="Add to Cart">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </button>
            @else
                <button type="button" disabled class="p-2.5 bg-slate-200 text-slate-400 rounded-xl cursor-not-allowed">
                    <i data-lucide="slash" class="w-4 h-4"></i>
                </button>
            @endif
        </div>
    </div>
</div>
