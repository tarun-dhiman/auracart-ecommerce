@extends('customer.layout')

@section('title', 'My Wishlist — AuraCart')

@section('customer_content')
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
    <div class="pb-4 border-b border-slate-100">
        <h2 class="text-base font-bold text-slate-900">Saved Wishlist</h2>
        <p class="text-xs text-slate-500">Products you've bookmarked for your collection.</p>
    </div>

    @if($wishlistItems->isEmpty())
        <div class="text-center py-16 text-slate-500 text-xs">
            <i data-lucide="heart" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p>Your wishlist is currently empty.</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-3 px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                Discover Items
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($wishlistItems as $item)
                @php $product = $item->product; @endphp
                <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-indigo-200 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-3">
                    <div class="aspect-square bg-slate-50 rounded-xl overflow-hidden relative">
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="absolute top-2 right-2">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-full bg-white/90 text-rose-500 hover:bg-white shadow transition" title="Remove">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 uppercase">{{ $product->category->name ?? '' }}</span>
                        <h3 class="text-xs font-bold text-slate-900 line-clamp-2 mt-0.5">
                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-indigo-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p class="text-sm font-black text-slate-900 mt-1">₹{{ number_format($product->effective_price, 2) }}</p>
                    </div>

                    <form method="POST" action="{{ route('wishlist.move-to-cart', $product->id) }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                            <span>Move to Cart</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $wishlistItems->links() }}
        </div>
    @endif
</div>
@endsection
