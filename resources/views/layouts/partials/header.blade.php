<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">

            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-900 flex items-center justify-center text-white shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-300"></i>
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight text-slate-900">AURA<span class="text-indigo-600">CART</span></span>
                    <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-600 -mt-1">Luxury Living</span>
                </div>
            </a>

            <!-- Desktop Search Bar with Autocomplete -->
            <div class="hidden md:flex flex-1 max-w-xl mx-4 relative" x-data="searchAutocomplete()">
                <div class="relative w-full">
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="fetchSuggestions()"
                           @focus="open = query.length >= 2"
                           @click.away="open = false"
                           placeholder="Search products, brands, categories..."
                           class="w-full pl-11 pr-10 py-2.5 bg-slate-100/90 border border-slate-200 rounded-full text-sm text-slate-900 placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100 transition duration-200">
                    <div class="absolute left-4 top-3 text-slate-600">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <button type="button" x-show="query.length > 0" @click="query = ''; open = false;" class="absolute right-3.5 top-3 text-slate-600 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div x-show="open" x-cloak class="absolute left-0 right-0 top-12 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 p-2">
                    <template x-if="results.products && results.products.length > 0">
                        <div>
                            <div class="px-3 py-1.5 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Products</div>
                            <template x-for="prod in results.products" :key="prod.name">
                                <a :href="prod.url" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-indigo-50 transition group">
                                    <img :src="prod.image" :alt="prod.name" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate group-hover:text-indigo-600" x-text="prod.name"></p>
                                        <p class="text-xs font-bold text-indigo-600" x-text="prod.price"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    <template x-if="results.categories && results.categories.length > 0">
                        <div class="border-t border-slate-100 pt-1 mt-1">
                            <div class="px-3 py-1.5 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Categories</div>
                            <template x-for="cat in results.categories" :key="cat.name">
                                <a :href="cat.url" class="flex items-center justify-between px-3 py-1.5 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                    <span x-text="cat.name"></span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600"></i>
                                </a>
                            </template>
                        </div>
                    </template>

                    <div class="border-t border-slate-100 mt-2 pt-2 px-3 py-1 bg-slate-50 rounded-xl text-center">
                        <a :href="'/products?q=' + encodeURIComponent(query)" class="text-xs font-bold text-indigo-600 hover:underline">
                            View all matching results &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Utility Navigation -->
            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('products.index') }}" class="hidden lg:inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition px-3 py-2 rounded-lg hover:bg-slate-50">
                    <i data-lucide="grid" class="w-4 h-4 text-slate-600"></i>
                    <span>Catalog</span>
                </a>

                <!-- Wishlist Icon -->
                <a href="{{ route('customer.wishlist') }}" class="relative p-2.5 text-slate-700 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition group" title="Wishlist">
                    <i data-lucide="heart" class="w-5 h-5 transition group-hover:scale-110"></i>
                    @auth
                        @php $wCount = Auth::user()->wishlistItems()->count(); @endphp
                        <span x-show="wishlistCount > 0 || {{ $wCount }} > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-rose-500 text-white rounded-full text-[11px] font-bold flex items-center justify-center ring-2 ring-white" x-text="wishlistCount || {{ $wCount }}"></span>
                    @endauth
                </a>

                <!-- Cart Drawer Trigger Button -->
                <button type="button" @click="openCartDrawer()" class="relative p-2.5 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition group" title="Shopping Cart">
                    <i data-lucide="shopping-bag" class="w-5 h-5 transition group-hover:scale-110"></i>
                    <span x-show="cartCount > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-indigo-600 text-white rounded-full text-[11px] font-bold flex items-center justify-center ring-2 ring-white" x-text="cartCount"></span>
                </button>

                <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>

                <!-- User Account / Login Dropdown -->
                @guest
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-indigo-600 px-3 py-2 rounded-lg hover:bg-slate-100 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center text-sm font-bold text-white bg-slate-900 hover:bg-indigo-600 px-4 py-2 rounded-xl transition duration-200 shadow-sm">
                            Register
                        </a>
                    </div>
                @else
                    <div class="relative" x-data="{ userMenu: false }">
                        <button type="button" @click="userMenu = !userMenu" @click.away="userMenu = false" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-medium text-slate-600 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-600"></i>
                        </button>

                        <div x-show="userMenu" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 divide-y divide-slate-100">
                            <div class="px-4 py-2">
                                <p class="text-xs text-slate-600">Signed in as</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                        <span>Admin Control Panel</span>
                                    </a>
                                @endif
                                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="user" class="w-4 h-4 text-slate-600"></i>
                                    <span>My Account</span>
                                </a>
                                <a href="{{ route('customer.orders') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="package" class="w-4 h-4 text-slate-600"></i>
                                    <span>My Orders</span>
                                </a>
                                <a href="{{ route('customer.addresses') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-600"></i>
                                    <span>Saved Addresses</span>
                                </a>
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest

            </div>
        </div>
    </div>
</header>
