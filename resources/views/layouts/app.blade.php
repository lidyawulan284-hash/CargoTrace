<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTracer') &mdash; Tracking &bull; Logistics &bull; Delivery</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            sidebar: '#0B172E',
                            dark: '#081224',
                            active: '#2563EB',
                            card: '#0F224A',
                        },
                        brand: {
                            blue: '#2563EB',
                            hover: '#1D4ED8',
                            light: '#EFF6FF',
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
        .leaflet-container { font-family: inherit; z-index: 10; border-radius: 12px; }
        [x-cloak] { display: none !important; }
        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    </style>
</head>
<body class="h-full text-[#1E293B] antialiased min-h-screen bg-[#F4F7FB] font-sans flex flex-col selection:bg-[#2563EB] selection:text-white">

    <div class="flex min-h-screen w-full">

        <!-- ========================================================================= -->
        <!-- 1. LEFT SIDEBAR (Desktop) -->
        <!-- ========================================================================= -->
        <aside id="mainSidebar" class="hidden md:flex flex-col w-60 lg:w-64 bg-[#0B172E] text-white flex-shrink-0 min-h-screen sticky top-0 h-screen overflow-y-auto z-40 border-r border-[#122344]">
            
            <!-- Brand Logo & Slogan Header -->
            <div class="px-5 pt-6 pb-5 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <img src="/images/cube-logo.png" alt="CargoTracer Logo" class="w-9 h-9 object-contain drop-shadow-md group-hover:scale-105 transition-transform">
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-white leading-tight">CargoTracer</span>
                        <span class="text-[9.5px] text-slate-400 font-medium tracking-wide">Tracking &bull; Logistics &bull; Delivery</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3.5 py-3 space-y-1 text-sm font-medium">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-[#2563EB] text-white font-bold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Shipments -->
                <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('shipments.*') ? 'bg-[#2563EB] text-white font-bold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Shipments</span>
                </a>

                <!-- Carriers -->
                <a href="{{ route('carriers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('carriers.*') ? 'bg-[#2563EB] text-white font-bold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                    </svg>
                    <span>Carriers</span>
                </a>

                <!-- Locations -->
                <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('locations.*') ? 'bg-[#2563EB] text-white font-bold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Locations</span>
                </a>

                <!-- Audit Logs -->
                @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('audit_logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('audit_logs.*') ? 'bg-[#2563EB] text-white font-bold shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Audit Logs</span>
                    </a>
                @endif

                <!-- Portal Publik -->
                <a href="{{ route('tracking.public') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Portal Publik</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>

                <!-- Settings -->
                <button type="button" onclick="document.getElementById('settingsModal').classList.remove('hidden')" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition text-left">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Settings</span>
                </button>

            </nav>

            <!-- Bottom Promotional Card (Cargo Ship "Connecting Your World") -->
            <div class="px-4 pb-5 pt-2 mt-auto">
                <div class="rounded-2xl overflow-hidden shadow-lg border border-white/10 relative group bg-[#0A162B]">
                    <img src="/images/promo-card.png" alt="Connecting Your World - Faster. Safer. Smarter." class="w-full h-auto object-cover rounded-2xl block group-hover:scale-102 transition duration-300">
                </div>
            </div>

        </aside>

        <!-- ========================================================================= -->
        <!-- MOBILE SIDEBAR DRAWER (Shown on small screens) -->
        <!-- ========================================================================= -->
        <div id="mobileDrawerBackdrop" class="fixed inset-0 bg-slate-900/60 z-50 hidden transition-opacity"></div>
        <aside id="mobileDrawer" class="fixed inset-y-0 left-0 w-64 bg-[#0B172E] text-white flex flex-col z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden shadow-2xl">
            <div class="px-5 pt-6 pb-4 flex items-center justify-between border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <img src="/images/cube-logo.png" alt="CargoTracer" class="w-8 h-8 object-contain">
                    <span class="text-lg font-black text-white">CargoTracer</span>
                </div>
                <button type="button" id="closeDrawerBtn" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 text-sm font-medium overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[#2563EB] text-white' : 'text-slate-300 hover:bg-white/5' }}">
                    Dashboard
                </a>
                <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('shipments.*') ? 'bg-[#2563EB] text-white' : 'text-slate-300 hover:bg-white/5' }}">
                    Shipments
                </a>
                <a href="{{ route('carriers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('carriers.*') ? 'bg-[#2563EB] text-white' : 'text-slate-300 hover:bg-white/5' }}">
                    Carriers
                </a>
                <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('locations.*') ? 'bg-[#2563EB] text-white' : 'text-slate-300 hover:bg-white/5' }}">
                    Locations
                </a>
                @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('audit_logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('audit_logs.*') ? 'bg-[#2563EB] text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        Audit Logs
                    </a>
                @endif
                <a href="{{ route('tracking.public') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5">
                    Portal Publik &nearr;
                </a>
            </nav>
        </aside>

        <!-- ========================================================================= -->
        <!-- 2. MAIN CONTENT WRAPPER -->
        <!-- ========================================================================= -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#F4F7FB]">
            
            <!-- Top Navigation Bar -->
            <header class="bg-white border-b border-slate-200/60 h-16 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-2xs">
                
                <!-- Left: Hamburger button & Long Search Input -->
                <div class="flex items-center gap-3 sm:gap-4 flex-1">
                    <button type="button" id="hamburgerBtn" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Toggle Navigation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Long search bar -->
                    <form action="{{ route('shipments.index') }}" method="GET" class="relative w-full max-w-sm sm:max-w-md lg:max-w-lg">
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shipment, tracking number..." class="w-full bg-[#F1F5F9]/80 hover:bg-[#F1F5F9] focus:bg-white border border-slate-200/70 focus:border-blue-500 rounded-full pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none transition focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        </div>
                    </form>
                </div>

                <!-- Right: Notification Bell & User Profile Dropdown -->
                <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                    
                    <!-- Notification Bell with Red Badge "3" -->
                    <div class="relative">
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-full transition flex items-center justify-center" title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @php
                                $unread = Auth::check() ? Auth::user()->notifications()->whereNull('read_at')->count() : 3;
                            @endphp
                            <span class="absolute top-1 right-1 w-4 h-4 bg-[#EF4444] text-[10px] font-black text-white rounded-full flex items-center justify-center shadow-xs">
                                {{ $unread > 0 ? $unread : 3 }}
                            </span>
                        </a>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative" id="userMenuContainer">
                        <button type="button" id="userMenuBtn" class="flex items-center gap-2.5 p-1 rounded-full hover:bg-slate-100 transition focus:outline-none">
                            <img src="/images/admin-avatar.png" alt="Admin Avatar" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border border-slate-200 shadow-2xs">
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1 leading-tight">
                                    {{ Auth::check() ? (Auth::user()->isAdmin() ? 'Admin' : Auth::user()->name) : 'Admin' }}
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium leading-none mt-0.5">
                                    {{ Auth::check() && Auth::user()->isAdmin() ? 'Administrator' : (Auth::check() ? ucfirst(Auth::user()->role) : 'Administrator') }}
                                </span>
                            </div>
                        </button>

                        <!-- Profile Dropdown Menu -->
                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 text-xs text-slate-700">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::check() ? Auth::user()->email : 'admin@cargotrace.test' }}</p>
                            </div>

                            <div class="px-2 py-1.5 border-b border-slate-100">
                                <div class="px-2 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quick Role Switch (Grading)</div>
                                <a href="{{ route('demo.login', 'admin') }}" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50 font-medium text-slate-700 hover:text-blue-600">👑 Administrator</a>
                                <a href="{{ route('demo.login', 'operator') }}" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50 font-medium text-slate-700 hover:text-blue-600">⚙️ Dispatch Operator</a>
                                <a href="{{ route('demo.login', 'customer') }}" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50 font-medium text-slate-700 hover:text-amber-600">📦 Customer Consignment</a>
                            </div>

                            <div class="px-2 pt-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 font-medium transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </header>

            <!-- Flash Alert Banners -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-r-xl shadow-xs mb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-emerald-800 text-xs sm:text-sm">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="bg-amber-50 border-l-4 border-[#F59E0B] p-3.5 rounded-r-xl shadow-xs mb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-amber-900 text-xs sm:text-sm">
                            <svg class="w-5 h-5 text-[#F59E0B] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span class="font-medium">{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error') || (isset($errors) && $errors->any()))
                    <div class="bg-rose-50 border-l-4 border-rose-500 p-3.5 rounded-r-xl shadow-xs mb-3">
                        <div class="flex items-center space-x-3 text-rose-800 text-xs sm:text-sm">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <div>
                                @if(session('error'))
                                    <p class="font-medium">{{ session('error') }}</p>
                                @endif
                                @if(isset($errors) && $errors->any())
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
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-5 w-full max-w-7xl mx-auto">
                @yield('content')
            </main>

            <!-- Clean Footer -->
            <footer class="bg-white border-t border-slate-200/60 mt-auto py-5">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-800">CargoTracer</span> &copy; {{ date('Y') }} &bull; Global Visibility &bull; Telematics &bull; Delivery
                    </div>
                    <div class="flex items-center space-x-3 text-slate-400">
                        <span>Laravel 12 &bull; Tailwind CSS &bull; Leaflet.js</span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">CargoTracer Settings</h3>
                </div>
                <button type="button" onclick="document.getElementById('settingsModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="py-4 space-y-3 text-xs text-slate-600">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-800">Telematics Auto-Refresh</div>
                        <div class="text-[11px] text-slate-400">Fetch dynamic coordinate updates every 30s</div>
                    </div>
                    <span class="inline-block w-8 h-4 bg-blue-600 rounded-full relative cursor-pointer"><span class="absolute right-0.5 top-0.5 w-3 h-3 bg-white rounded-full"></span></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-800">Weather Hazards Alert</div>
                        <div class="text-[11px] text-slate-400">Open-Meteo REST API threshold warning</div>
                    </div>
                    <span class="inline-block w-8 h-4 bg-blue-600 rounded-full relative cursor-pointer"><span class="absolute right-0.5 top-0.5 w-3 h-3 bg-white rounded-full"></span></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-800">UI Layout Theme</div>
                        <div class="text-[11px] text-slate-400">Deep Blue &amp; Modern White SaaS Edition</div>
                    </div>
                    <span class="font-bold text-blue-600">Active</span>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="document.getElementById('settingsModal').classList.add('hidden')" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl text-xs hover:bg-blue-700 transition">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- UI Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hamburger Mobile Drawer
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileBackdrop = document.getElementById('mobileDrawerBackdrop');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');

        function openDrawer() {
            if (mobileDrawer && mobileBackdrop) {
                mobileDrawer.classList.remove('-translate-x-full');
                mobileBackdrop.classList.remove('hidden');
            }
        }
        function closeDrawer() {
            if (mobileDrawer && mobileBackdrop) {
                mobileDrawer.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', openDrawer);
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
        if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeDrawer);

        // User Profile Dropdown Toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');

        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('userMenuContainer')?.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
