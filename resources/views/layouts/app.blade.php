<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AuraCart — Luxury Living & Conscious Essentials')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
                            100: '#ebf0fe',
                            200: '#cedaff',
                            500: '#4f46e5',
                            600: '#4338ca',
                            700: '#3730a3',
                            900: '#1e1b4b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen" x-data="auraStore()">

    <!-- Announcement Bar -->
    @include('layouts.partials.announcement')

    <!-- Main Navigation Header -->
    @include('layouts.partials.header')

    <!-- Flash Messages Banner -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white text-sm font-medium py-2.5 px-4 text-center shadow-inner flex items-center justify-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-600 text-white text-sm font-medium py-2.5 px-4 text-center shadow-inner flex items-center justify-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Mini Cart Drawer -->
    @include('layouts.partials.drawer')

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Global Scripts -->
    @include('layouts.partials.scripts')
    @stack('scripts')
</body>
</html>
