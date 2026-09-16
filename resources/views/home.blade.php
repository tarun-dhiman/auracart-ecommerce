@extends('layouts.app')

@section('title', 'AuraCart — Modern Luxury & Conscious Lifestyle')

@section('content')

    <!-- Hero Section / Dynamic Banner Carousel -->
    <section class="relative bg-slate-900 text-white overflow-hidden">
        @if($heroBanners->isNotEmpty())
            @php $hero = $heroBanners->first(); @endphp
            <div class="relative min-h-[500px] lg:min-h-[580px] flex items-center">
                <!-- Background Image with Dark Vignette -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="w-full h-full object-cover object-center opacity-40 mix-blend-luminosity">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
                </div>

                <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                    <div class="max-w-2xl space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>New Season Collections 2026</span>
                        </div>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15]">
                            {{ $hero->title }}
                        </h1>
                        <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-light">
                            {{ $hero->subtitle }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4 pt-4">
                            <a href="{{ $hero->link_url ?: route('products.index') }}" class="px-7 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-indigo-600/30 hover:scale-105 transition duration-200 flex items-center gap-2">
                                <span>{{ $hero->button_text ?: 'Explore Collection' }}</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('products.index', ['sort' => 'bestseller']) }}" class="px-6 py-3.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/20 text-sm font-bold rounded-2xl transition duration-200">
                                View Best Sellers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Why Choose Us / Trust Badges Section -->
    <section class="border-b border-slate-200 bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Express Delivery</h4>
                        <p class="text-xs text-slate-500">Free delivery on orders over ₹999</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">100% Genuine</h4>
                        <p class="text-xs text-slate-500">Verified manufacturer warranty</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">7-Day Easy Returns</h4>
                        <p class="text-xs text-slate-500">Hassle-free doorstep pickup</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Secure Payments</h4>
                        <p class="text-xs text-slate-500">COD, Razorpay & Stripe encrypted</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Categories Showcase -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Curated Categories</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Explore By Department</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                <span>View Full Catalog</span>
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredCategories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="group relative rounded-3xl overflow-hidden bg-white border border-slate-200/80 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h3 class="text-base font-bold group-hover:text-amber-300 transition-colors">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-300">{{ $cat->products_count }} Products</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-12 bg-slate-100/60 border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Handpicked Quality</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Featured Essentials</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                    <span>See all</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $prod)
                    <x-product-card :product="$prod" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Promotional Split Banner Strip -->
    @if($promoBanners->isNotEmpty())
        @php $promo = $promoBanners->first(); @endphp
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-slate-950 text-white shadow-2xl">
                <div class="absolute inset-0">
                    <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="w-full h-full object-cover opacity-30">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
                </div>

                <div class="relative p-8 sm:p-14 max-w-xl space-y-4">
                    <span class="inline-block px-3 py-1 rounded-full bg-amber-400 text-slate-950 text-[11px] font-black uppercase tracking-wider">
                        Special Campaign
                    </span>
                    <h3 class="text-2xl sm:text-4xl font-black leading-tight">{{ $promo->title }}</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">{{ $promo->subtitle }}</p>
                    <div class="pt-2">
                        <a href="{{ $promo->link_url ?: route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-900 hover:bg-amber-400 font-bold text-xs rounded-xl transition duration-200">
                            <span>{{ $promo->button_text ?: 'Shop Campaign' }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Best Sellers Section -->
    <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Customer Favorites</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Best Selling Products</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'bestseller']) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                <span>View All Best Sellers</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($bestSellers as $prod)
                <x-product-card :product="$prod" />
            @endforeach
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="py-12 bg-slate-100/60 border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Just Dropped</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">New Arrivals</h2>
                </div>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                    <span>Explore Fresh Drops</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newArrivals as $prod)
                    <x-product-card :product="$prod" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Verified Customer Testimonials -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Social Proof</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">What Our Patrons Say</h2>
            <p class="text-sm text-slate-500 mt-2">Authentic feedback from verified collectors across India.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimonials as $t)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center text-amber-400 gap-1 mb-3">
                            @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                            @endfor
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-2">"{{ $t->title }}"</h4>
                        <p class="text-xs text-slate-600 leading-relaxed italic mb-4">"{{ $t->comment }}"</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($t->user->name ?? 'User', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $t->user->name ?? 'Verified Buyer' }}</p>
                                <p class="text-[10px] text-slate-500 truncate max-w-[130px]">{{ $t->product->name ?? 'Item' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <i data-lucide="check" class="w-3 h-3"></i> Verified
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Quick View Modal Container -->
    @include('components.quickview-modal')

@endsection
