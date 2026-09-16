<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control Hub — AuraCart Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f7ff',
                            500: '#4f46e5',
                            600: '#4338ca',
                            700: '#3730a3',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="flex h-full overflow-hidden bg-slate-950 text-slate-100" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between flex-shrink-0 z-30 fixed lg:static inset-y-0 left-0 transform lg:transform-none transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <div>
            <!-- Admin Logo Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-indigo-700 flex items-center justify-center text-white shadow-md shadow-indigo-900/50">
                        <i data-lucide="shield-check" class="w-5 h-5 text-amber-300"></i>
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-white">AURA<span class="text-indigo-400">HUB</span></span>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-indigo-400 -mt-1">Administration</span>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-160px)] custom-scrollbar text-xs font-semibold">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3">Store Catalog</div>

                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Products Management</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="folder-tree" class="w-4 h-4"></i>
                    <span>Categories & Subcats</span>
                </a>

                <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.inventory*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="boxes" class="w-4 h-4"></i>
                    <span>Inventory & Stock</span>
                </a>

                <div class="pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3">Operations</div>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Orders & Fulfillment</span>
                </a>

                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.customers*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Customers Directory</span>
                </a>

                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.coupons*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                    <span>Coupons & Promos</span>
                </a>

                <div class="pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3">Content & Control</div>

                <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.banners*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    <span>Banners & Marketing</span>
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reviews*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="star" class="w-4 h-4"></i>
                    <span>Review Moderation</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Store Settings</span>
                </a>
            </div>
        </div>

        <!-- Admin Profile Footer & Storefront Link -->
        <div class="p-4 border-t border-slate-800 space-y-2">
            <a href="{{ route('home') }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 text-xs font-bold transition">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-indigo-400"></i>
                <span>Open Storefront</span>
            </a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-950/40 text-xs font-bold transition text-left">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-950">

        <!-- Top Header Bar -->
        <header class="h-20 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 flex items-center justify-between px-4 sm:px-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-sm font-bold text-white">@yield('header_title', 'Overview')</h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/60 text-xs">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="font-mono text-slate-300">Live Production</span>
                </div>

                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-inner">
                    A
                </div>
            </div>
        </header>

        <!-- Flash Message Banners -->
        @if(session('success'))
            <div class="bg-emerald-600/90 text-white text-xs font-semibold py-2.5 px-6 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-600/90 text-white text-xs font-semibold py-2.5 px-6 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Scrollable Workspace Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8 custom-scrollbar">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
