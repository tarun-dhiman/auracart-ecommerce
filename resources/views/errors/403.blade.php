<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Restricted | AuraCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-gradient-to-tr from-slate-950 via-indigo-950/40 to-slate-950">

    <div class="w-full max-w-lg bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-xl text-center space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center mx-auto shadow-inner">
            <i data-lucide="shield-alert" class="w-8 h-8"></i>
        </div>

        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">
                Error 403 &bull; Unauthorized
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Admin Privileges Required</h1>
            <p class="text-sm text-slate-400 mt-2 leading-relaxed">
                You do not currently have administrative permissions to view this section. If you are a store administrator, please sign in using your admin credentials.
            </p>
        </div>

        <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700/60 text-xs text-left space-y-1.5">
            <p class="font-bold text-slate-200">Default Admin Credentials:</p>
            <p class="font-mono text-indigo-400">admin@auracart.com / password123</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('admin.login') }}" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-indigo-900/40 flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Sign In to Admin Hub
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Storefront
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
