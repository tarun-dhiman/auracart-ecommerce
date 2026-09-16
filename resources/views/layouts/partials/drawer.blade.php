<div x-show="cartDrawerOpen" x-cloak class="relative z-50">
    <!-- Backdrop -->
    <div x-show="cartDrawerOpen"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cartDrawerOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div x-show="cartDrawerOpen"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md bg-white shadow-2xl flex flex-col">

            <!-- Drawer Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-indigo-600"></i>
                    <h2 class="text-lg font-bold text-slate-900">Your Cart (<span x-text="cartCount"></span>)</h2>
                </div>
                <button type="button" @click="cartDrawerOpen = false" class="p-2 text-slate-600 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Drawer Items List -->
            <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar">
                <template x-if="drawerItems.length === 0">
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="shopping-cart" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">Your cart is empty</h3>
                        <p class="text-xs text-slate-600 mb-6">Discover something exquisite from our curated collections.</p>
                        <a href="{{ route('products.index') }}" @click="cartDrawerOpen = false" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                            <span>Browse Catalog</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </template>

                <template x-for="item in drawerItems" :key="item.id">
                    <div class="flex gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded-xl border border-slate-200 bg-white">
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 truncate" x-text="item.name"></h4>
                                <p class="text-xs text-slate-600" x-show="item.variant" x-text="item.variant"></p>
                                <p class="text-sm font-black text-indigo-600 mt-1" x-text="item.price"></p>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-slate-600 font-semibold" x-text="'Qty: ' + item.quantity"></span>
                                <button type="button" @click="removeCartItem(item.id)" class="text-xs text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Drawer Footer -->
            <div x-show="drawerItems.length > 0" class="p-5 border-t border-slate-100 bg-slate-50/80 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-600 font-medium">Estimated Subtotal</span>
                    <span class="text-base font-black text-slate-900" x-text="'?' + Number(drawerSubtotal).toFixed(2)"></span>
                </div>
                <p class="text-[11px] text-slate-600">Taxes, coupon discounts & shipping calculated at checkout.</p>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="{{ route('cart.index') }}" @click="cartDrawerOpen = false" class="text-center py-3 px-4 bg-white border border-slate-300 text-slate-800 text-xs font-bold rounded-xl hover:bg-slate-100 transition">
                        View Full Cart
                    </a>
                    <a href="{{ route('checkout.index') }}" class="text-center py-3 px-4 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition shadow-md shadow-slate-200">
                        Checkout Now
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
