<footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">

            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-indigo-700 flex items-center justify-center text-white">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-300"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-white">AURA<span class="text-indigo-400">CART</span></span>
                </div>
                <p class="text-sm text-slate-400 max-w-sm leading-relaxed">
                    Curating certified luxury electronics, conscious lifestyle essentials, and modern designer goods with transparent provenance and express doorstep delivery.
                </p>
                <div class="flex items-center gap-3 pt-2 text-slate-400">
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition"><i data-lucide="instagram" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition"><i data-lucide="facebook" class="w-4 h-4"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                </div>
            </div>

            <!-- Navigation Columns -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Shop Collections</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-white transition">Electronics & Audio</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-white transition">Minimalist Fashion</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'wellness-personal-care']) }}" class="hover:text-white transition">Personal Wellness</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'home-living']) }}" class="hover:text-white transition">Home & Interior</a></li>
                    <li><a href="{{ route('products.index', ['sort' => 'bestseller']) }}" class="hover:text-white transition">Best Sellers</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Customer Care</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('orders.track') }}" class="hover:text-white transition">Track Your Order</a></li>
                    <li><a href="{{ route('customer.dashboard') }}" class="hover:text-white transition">Customer Portal</a></li>
                    <li><a href="{{ route('customer.orders') }}" class="hover:text-white transition">Returns & Refunds</a></li>
                    <li><a href="#" class="hover:text-white transition">Shipping Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Privacy & Terms</a></li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Aura Insider</h4>
                <p class="text-xs text-slate-400 mb-3">Subscribe for exclusive early-access releases and seasonal private sales.</p>
                <form @submit.prevent="alert('Thank you for subscribing to AuraCart Insider!')" class="space-y-2">
                    <input type="email" required placeholder="Enter your email" class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition">
                        Subscribe
                    </button>
                </form>
            </div>

        </div>

        <!-- Bottom Legal & Payment Badges -->
        <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} AuraCart E-Commerce Private Limited. All rights reserved.</p>
            <div class="flex items-center gap-3">
                <span class="px-2 py-1 bg-slate-800 text-slate-400 rounded text-[10px] font-mono">CASH ON DELIVERY</span>
                <span class="px-2 py-1 bg-slate-800 text-slate-400 rounded text-[10px] font-mono">RAZORPAY</span>
                <span class="px-2 py-1 bg-slate-800 text-slate-400 rounded text-[10px] font-mono">STRIPE</span>
                <span class="px-2 py-1 bg-slate-800 text-slate-400 rounded text-[10px] font-mono">UPI / CARDS</span>
            </div>
        </div>
    </div>
</footer>
