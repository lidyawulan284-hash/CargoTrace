<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CargoTrace Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-slate-800">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 border border-slate-100">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-600 to-blue-500 items-center justify-center shadow-lg shadow-sky-500/30 mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Sign In to <span class="text-sky-600">CargoTrace</span></h1>
            <p class="text-xs text-slate-500 mt-1">Global Shipment Visibility & Tracking Platform</p>
        </div>

        @if(session('info'))
            <div class="bg-blue-50 text-blue-800 p-3 rounded-xl text-xs mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 text-rose-800 p-3 rounded-xl text-xs mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                       placeholder="you@domain.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-sky-600 focus:ring-sky-500 mr-2">
                    Remember me
                </label>
                <a href="{{ route('home') }}" class="text-sky-600 hover:underline">Track without login</a>
            </div>

            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-semibold text-sm shadow-lg shadow-slate-900/20 transition duration-150">
                Sign In
            </button>
        </form>

        <!-- Quick 1-Click Grading / Demo Accounts Box (Slide 19) -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">
                Instant Grading One-Click Demo
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('demo.login', 'admin') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 transition text-center group">
                    <span class="text-base group-hover:scale-110 transition">👑</span>
                    <span class="text-[11px] font-bold mt-1">Admin</span>
                    <span class="text-[9px] text-purple-600">Full Access</span>
                </a>
                <a href="{{ route('demo.login', 'operator') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 transition text-center group">
                    <span class="text-base group-hover:scale-110 transition">⚙️</span>
                    <span class="text-[11px] font-bold mt-1">Operator</span>
                    <span class="text-[9px] text-blue-600">Ops & Tracking</span>
                </a>
                <a href="{{ route('demo.login', 'customer') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition text-center group">
                    <span class="text-base group-hover:scale-110 transition">📦</span>
                    <span class="text-[11px] font-bold mt-1">Customer</span>
                    <span class="text-[9px] text-emerald-600">My Shipments</span>
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-sky-600 font-semibold hover:underline">Register here</a>
        </div>
    </div>

</body>
</html>
