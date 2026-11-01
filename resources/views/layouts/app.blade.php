<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTrace') - Global Shipment Visibility Platform</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc7fb',
                            400: '#36abf7',
                            500: '#0c90e7',
                            600: '#0172c6',
                            700: '#025ba0',
                            800: '#064d84',
                            900: '#0b416e',
                            950: '#072a49',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .leaflet-container { font-family: inherit; z-index: 10; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900 border-b border-slate-800 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-sky-400 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5">
                            Cargo<span class="text-sky-400">Trace</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 block -mt-1 font-semibold">Global Visibility</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-1 ml-8">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-sky-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Dashboard
                    </a>
                    <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('shipments.*') ? 'bg-slate-800 text-sky-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Shipments
                    </a>
                    <a href="{{ route('carriers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('carriers.*') ? 'bg-slate-800 text-sky-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Carriers
                    </a>
                    <a href="{{ route('locations.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('locations.*') ? 'bg-slate-800 text-sky-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Locations
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('audit_logs.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('audit_logs.*') ? 'bg-slate-800 text-sky-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                            Audit Logs
                        </a>
                    @endif
                    <a href="{{ route('home') }}" target="_blank" class="px-3 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-slate-200 transition flex items-center gap-1">
                        <span>Public Portal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </nav>
            </div>

            <!-- Right: Notifications & User Profile -->
            <div class="flex items-center space-x-3">
                
                <!-- In-App Notifications Bell (Slide 10) -->
                @php
                    $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                @endphp
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg transition" title="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-[10px] font-bold text-white rounded-full flex items-center justify-center animate-pulse">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- User Badge -->
                <div class="hidden sm:flex items-center space-x-2 border-l border-slate-800 pl-3">
                    <div class="text-right">
                        <div class="text-sm font-semibold text-white leading-none">{{ Auth::user()->name }}</div>
                        <span class="inline-block mt-0.5 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-purple-950 text-purple-300 border border-purple-800' : (Auth::user()->isOperator() ? 'bg-blue-950 text-blue-300 border border-blue-800' : 'bg-emerald-950 text-emerald-300 border border-emerald-800') }}">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                </div>

                <!-- Role Switcher Quick Menu (Grading Helper) -->
                <div class="relative group">
                    <button class="bg-slate-800 text-slate-300 hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-medium border border-slate-700 flex items-center gap-1.5 transition">
                        <span>Switch Role</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="absolute right-0 mt-1 w-44 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl py-1 text-xs hidden group-hover:block z-50">
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase text-slate-400 border-b border-slate-800">Demo Role Switch</div>
                        <a href="{{ route('demo.login', 'admin') }}" class="block px-3 py-2 text-slate-200 hover:bg-slate-800 hover:text-purple-400">👑 Switch to Admin</a>
                        <a href="{{ route('demo.login', 'operator') }}" class="block px-3 py-2 text-slate-200 hover:bg-slate-800 hover:text-blue-400">⚙️ Switch to Operator</a>
                        <a href="{{ route('demo.login', 'customer') }}" class="block px-3 py-2 text-slate-200 hover:bg-slate-800 hover:text-emerald-400">📦 Switch to Customer</a>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition" title="Log Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Flash Alert Banners -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-3 text-emerald-800">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-3 text-amber-800">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm mb-4">
                <div class="flex items-center space-x-3 text-rose-800">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        @if(session('error'))
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <div>
                <span class="font-bold text-slate-700">CargoTrace</span> &copy; {{ date('Y') }} &bull; Web Programming 2 UAS Final Project Platform.
            </div>
            <div class="flex items-center space-x-4">
                <span>Stack: Laravel 12 &bull; MariaDB &bull; Tailwind &bull; Leaflet &bull; Open-Meteo API</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
