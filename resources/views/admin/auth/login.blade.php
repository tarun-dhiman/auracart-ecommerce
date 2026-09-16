<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Hub — AuraCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-gradient-to-tr from-slate-950 via-indigo-950/40 to-slate-950">

    <div class="w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-xl space-y-6">

        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center mx-auto mb-2 shadow-inner">
                <i data-lucide="shield-alert" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">AuraCart Management</h1>
            <p class="text-xs text-slate-400">Restricted Administration & Operations Portal</p>
        </div>

        @if(session('error'))
            <div class="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @auth
            @if(!auth()->user()->isAdmin())
                <div class="p-3 bg-amber-500/10 border border-amber-500/30 text-amber-300 rounded-xl text-xs space-y-1">
                    <div class="flex items-center gap-2 font-bold">
                        <i data-lucide="info" class="w-4 h-4 text-amber-400 flex-shrink-0"></i>
                        <span>Signed in as Customer: {{ auth()->user()->name }}</span>
                    </div>
                    <p class="text-[11px] text-amber-200/80">
                        Admin access requires admin credentials. Use the auto-fill button below or sign out.
                    </p>
                </div>
            @endif
        @endauth

        <!-- Quick Demo Fill -->
        <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700/60 text-xs flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-200">Demo Admin Credentials:</p>
                <p class="text-[11px] font-mono text-indigo-400">admin@auracart.com / password123</p>
            </div>
            <button type="button" 
                    onclick="document.getElementById('email').value='admin@auracart.com'; document.getElementById('password').value='password123';"
                    class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] rounded-lg transition">
                Auto-fill
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-300 mb-1">Admin Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full text-xs px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-300 mb-1">Security Password</label>
                <input type="password" id="password" name="password" required class="w-full text-xs px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-700 bg-slate-800">
                    <span>Keep session active</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition duration-200 shadow-lg shadow-indigo-600/30">
                Authenticate & Enter Hub
            </button>
        </form>

        <div class="pt-2 text-center text-xs text-slate-500 border-t border-slate-800/80">
            <a href="{{ route('home') }}" class="text-slate-400 hover:text-white flex items-center justify-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Return to Storefront</span>
            </a>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
