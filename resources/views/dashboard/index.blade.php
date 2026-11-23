@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- CSS Keyframes for Concentric Radar Pulse Rings -->
    <style>
        @keyframes radarPulse {
            0% { transform: scale(0.35); opacity: 0.9; }
            50% { opacity: 0.45; }
            100% { transform: scale(2.4); opacity: 0; }
        }
        .animate-radar-1 { animation: radarPulse 2.2s cubic-bezier(0.1, 0.4, 0.6, 1) infinite; }
        .animate-radar-2 { animation: radarPulse 2.2s cubic-bezier(0.1, 0.4, 0.6, 1) 0.7s infinite; }
        .animate-radar-3 { animation: radarPulse 2.2s cubic-bezier(0.1, 0.4, 0.6, 1) 1.4s infinite; }

        @keyframes userGpsPulse {
            0% { transform: scale(0.35); opacity: 1; }
            60% { opacity: 0.4; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        .animate-user-gps { animation: userGpsPulse 2s cubic-bezier(0.1, 0.4, 0.6, 1) infinite; }
    </style>

    <!-- ========================================================================= -->
    <!-- 1. WELCOME BANNER (Gradient Light Blue with Cargo Airplane & System Status) -->
    <!-- ========================================================================= -->
    <div class="rounded-2xl bg-gradient-to-r from-[#DBEDFF] via-[#CFE6FF] to-[#C2E0FE] p-6 sm:p-7 relative overflow-hidden border border-blue-100/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Left: Status pill, Title & Subtitle -->
        <div class="z-10 max-w-xl">
            <!-- Green Online Badge & Role Mode -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#DCFCE7] text-[#15803D] mb-3 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                <span>System Online</span>
                <span class="text-slate-400 font-normal ml-1">&bull;</span>
                <span class="text-emerald-900 font-bold ml-0.5">
                    @if(Auth::check() && Auth::user()->isAdmin())
                        Admin Operations Dashboard
                    @elseif(Auth::check() && Auth::user()->isOperator())
                        Operator Dispatch Dashboard
                    @else
                        Customer Cargo Dashboard
                    @endif
                </span>
            </div>

            <!-- Welcome Heading -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B1528] tracking-tight">
                Welcome to CargoTracer Dashboard
            </h1>

            <!-- Subtitle -->
            <p class="text-xs sm:text-sm text-slate-600 mt-1 font-normal">
                Monitor and manage your shipments with ease and efficiency.
            </p>
        </div>

        <!-- Right: Flying Cargo Airplane against Faint Clouds -->
        <div class="relative flex-shrink-0 self-end md:self-center -mb-6 md:mb-0 -mr-6 md:mr-2">
            <img src="/images/banner-plane.png" alt="Cargo Aircraft" class="h-28 sm:h-32 md:h-36 object-contain pointer-events-none select-none drop-shadow-sm">
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 2. HORIZONTAL SUMMARY STAT CARDS (5 Cards with Sparklines & Trend Arrows) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        
        <!-- Card 1: Total Shipments -->
        <div class="bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-4 flex flex-col justify-between h-[126px] relative overflow-hidden group hover:border-blue-200 transition">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-medium text-slate-500 block">Total Shipments</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight leading-tight block">{{ $metrics['total_shipments'] ?? 5 }}</span>
                </div>
            </div>
            <div class="flex items-end justify-between pt-1">
                <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                    <span>&uarr;</span> 12%
                </span>
                <!-- Mini Blue Sparkline -->
                <svg viewBox="0 0 70 24" class="w-16 h-6 overflow-visible">
                    <defs>
                        <linearGradient id="sparkBlue" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,20 Q18,22 35,12 T68,4 L68,24 L0,24 Z" fill="url(#sparkBlue)"/>
                    <path d="M0,20 Q18,22 35,12 T68,4" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: In Transit -->
        <div class="bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-4 flex flex-col justify-between h-[126px] relative overflow-hidden group hover:border-emerald-200 transition">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                    </svg>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-medium text-slate-500 block">In Transit</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight leading-tight block">{{ $metrics['in_transit'] ?? 3 }}</span>
                </div>
            </div>
            <div class="flex items-end justify-between pt-1">
                <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                    <span>&uarr;</span> 8%
                </span>
                <!-- Mini Green Sparkline -->
                <svg viewBox="0 0 70 24" class="w-16 h-6 overflow-visible">
                    <defs>
                        <linearGradient id="sparkGreen" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10B981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10B981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,18 Q18,20 35,11 T68,5 L68,24 L0,24 Z" fill="url(#sparkGreen)"/>
                    <path d="M0,18 Q18,20 35,11 T68,5" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Delivered -->
        <div class="bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-4 flex flex-col justify-between h-[126px] relative overflow-hidden group hover:border-amber-200 transition">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-medium text-slate-500 block">Delivered</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight leading-tight block">{{ $metrics['delivered'] ?? 1 }}</span>
                </div>
            </div>
            <div class="flex items-end justify-between pt-1">
                <span class="text-xs font-bold text-slate-400 flex items-center gap-0.5">
                    <span>&uarr;</span> 0%
                </span>
                <!-- Mini Amber Sparkline -->
                <svg viewBox="0 0 70 24" class="w-16 h-6 overflow-visible">
                    <defs>
                        <linearGradient id="sparkAmber" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,16 Q22,17 40,9 T68,8 L68,24 L0,24 Z" fill="url(#sparkAmber)"/>
                    <path d="M0,16 Q22,17 40,9 T68,8" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Exceptions -->
        <div class="bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-4 flex flex-col justify-between h-[126px] relative overflow-hidden group hover:border-rose-200 transition">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-medium text-slate-500 block">Exceptions</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight leading-tight block">{{ $metrics['open_exceptions'] ?? 2 }}</span>
                </div>
            </div>
            <div class="flex items-end justify-between pt-1">
                <span class="text-xs font-bold text-rose-600 flex items-center gap-0.5">
                    <span>&uarr;</span> 33%
                </span>
                <!-- Mini Rose Sparkline -->
                <svg viewBox="0 0 70 24" class="w-16 h-6 overflow-visible">
                    <defs>
                        <linearGradient id="sparkRose" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#EF4444" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#EF4444" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,22 Q20,20 40,8 T68,6 L68,24 L0,24 Z" fill="url(#sparkRose)"/>
                    <path d="M0,22 Q20,20 40,8 T68,6" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 5: On-Time KPI -->
        <div class="bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-4 flex flex-col justify-between h-[126px] relative overflow-hidden group hover:border-purple-200 transition">
            <div class="flex items-start justify-between">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-medium text-slate-500 block">On-Time KPI</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tight leading-tight block">{{ round($metrics['on_time_rate'] ?? 100) }}%</span>
                </div>
            </div>
            <div class="flex items-end justify-between pt-1">
                <span class="text-xs font-bold text-slate-400 flex items-center gap-0.5">
                    <span>&uarr;</span> 0%
                </span>
                <!-- Mini Purple Sparkline -->
                <svg viewBox="0 0 70 24" class="w-16 h-6 overflow-visible">
                    <defs>
                        <linearGradient id="sparkPurple" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#8B5CF6" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#8B5CF6" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,15 Q18,22 35,14 T68,10 L68,24 L0,24 Z" fill="url(#sparkPurple)"/>
                    <path d="M0,15 Q18,22 35,14 T68,10" fill="none" stroke="#8B5CF6" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. BOTTOM SPLIT GRID (Live Shipment Tracking Map & Recent Shipments) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ===================================================================== -->
        <!-- BOTTOM LEFT: Live Interactive Shipment Tracking Map (lg:col-span-7) -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-5 flex flex-col justify-between">
            
            <!-- Map Card Header with Real-Time Indicator & Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-50 text-[#2479E8] flex items-center justify-center flex-shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Shipment Tracking Map</h2>
                            <!-- Live Status Indicator -->
                            <span id="liveStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wide uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span id="liveStatusText">LIVE SIMULATION</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-normal">Real-time global flight telematics &amp; geodesic route tracking</p>
                    </div>
                </div>

                <!-- Shipment Selector Dropdown -->
                <div class="flex items-center gap-2">
                    <label for="liveShipmentSelector" class="text-[11px] font-bold text-slate-400 hidden sm:inline">Track:</label>
                    <div class="relative min-w-[210px] sm:min-w-[240px]">
                        <select id="liveShipmentSelector" class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-slate-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            @forelse($mapShipments as $ms)
                                <option value="{{ $ms->id }}" {{ $ms->shipment_number === 'CT-2026-SEA-8841' ? 'selected' : '' }}>
                                    {{ $ms->shipment_number }} &bull; {{ $ms->origin->city }} &rarr; {{ $ms->destination->city }}
                                </option>
                            @empty
                                <option value="">No shipments available</option>
                            @endforelse
                        </select>
                    </div>
                </div>
            </div>

            <!-- Map Playback & Live Telematics Controls Toolbar -->
            <div class="flex items-center justify-between gap-2 py-2.5 text-xs border-b border-slate-100 flex-wrap">
                
                <!-- Playback Buttons: Play/Pause, Reset, Auto-Follow, Speed, & LIVE LOKASI SAYA -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <!-- Play / Pause Button -->
                    <button type="button" id="playPauseBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#2479E8] hover:bg-[#1D6AD4] text-white font-bold rounded-lg transition shadow-2xs text-[11px]">
                        <svg id="playIcon" class="w-3.5 h-3.5 hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                        <svg id="pauseIcon" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span id="playPauseText">Pause</span>
                    </button>

                    <!-- Reset Flight Button -->
                    <button type="button" id="resetFlightBtn" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition text-[11px]" title="Reset Aircraft Position">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Reset</span>
                    </button>

                    <!-- Auto-Follow Camera Toggle -->
                    <button type="button" id="autoFollowBtn" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 text-[#2479E8] border border-blue-200 font-bold rounded-lg transition text-[11px]" title="Toggle Camera Auto-Follow Aircraft">
                        <span id="followDot" class="w-2 h-2 rounded-full bg-[#2479E8] animate-pulse"></span>
                        <span id="followText">Auto-Follow: ON</span>
                    </button>

                    <!-- Simulation Speed Selector -->
                    <div class="flex items-center rounded-lg border border-slate-200 overflow-hidden text-[10px] font-bold">
                        <button type="button" class="speed-btn px-2 py-1 bg-white text-slate-600 hover:bg-slate-50 transition" data-speed="1">1x</button>
                        <button type="button" class="speed-btn px-2 py-1 bg-[#2479E8] text-white transition" data-speed="2">2x</button>
                        <button type="button" class="speed-btn px-2 py-1 bg-white text-slate-600 hover:bg-slate-50 transition" data-speed="5">5x</button>
                    </div>

                    <!-- Divider -->
                    <div class="hidden sm:block h-4 w-px bg-slate-200 mx-1"></div>

                    <!-- LIVE LOKASI SAYA (Real-Time GPS Tracking) -->
                    <button type="button" id="liveUserGpsBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 font-bold rounded-lg transition shadow-2xs text-[11px]" title="Aktifkan Live Lokasi GPS Perangkat Anda">
                        <span class="relative flex h-2.5 w-2.5">
                            <span id="userGpsPing" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 hidden"></span>
                            <span id="userGpsDot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600"></span>
                        </span>
                        <span id="userGpsText">Live Lokasi Saya</span>
                    </button>

                    <!-- Camera Focus Selector when GPS is active -->
                    <div id="cameraFocusGroup" class="hidden items-center rounded-lg border border-slate-200 overflow-hidden text-[10px] font-bold">
                        <button type="button" id="focusCargoBtn" class="px-2 py-1 bg-[#2479E8] text-white transition" title="Kamera fokus ke Kargo">✈️ Kargo</button>
                        <button type="button" id="focusUserBtn" class="px-2 py-1 bg-white text-slate-600 hover:bg-slate-50 transition" title="Kamera fokus ke Lokasi Saya">📍 Saya</button>
                        <button type="button" id="focusAllBtn" class="px-2 py-1 bg-white text-slate-600 hover:bg-slate-50 transition" title="Tampilkan Keduanya">🌐 Keduanya</button>
                    </div>
                </div>

                <!-- Live Corridor Quick Tag -->
                <div class="text-[11px] font-bold text-slate-700 flex items-center gap-2">
                    <span class="text-slate-400 font-normal">Airway:</span>
                    <span id="quickAirwayText" class="text-blue-600 font-extrabold">Jakarta &rarr; Rotterdam</span>
                </div>

            </div>

            <!-- Realistic Map Canvas (Sleek dark frame with Layer Switcher) -->
            <div class="relative w-full h-[410px] sm:h-[460px] rounded-2xl overflow-hidden mt-3 border border-slate-200/80 bg-slate-900 shadow-inner">
                
                <!-- Leaflet Live Map Engine -->
                <div id="liveShipmentMap" class="w-full h-full z-10"></div>

                <!-- Top-Left: White Vertical Pill-Shaped Zoom Control -->
                <div class="absolute top-4 left-4 flex flex-col bg-white/95 backdrop-blur-xs rounded-xl shadow-lg border border-slate-200/80 overflow-hidden z-20">
                    <button type="button" id="mapZoomIn" class="w-8 h-8 flex items-center justify-center text-slate-700 hover:bg-slate-50 font-black text-sm border-b border-slate-100 transition" title="Zoom in">+</button>
                    <button type="button" id="mapZoomOut" class="w-8 h-8 flex items-center justify-center text-slate-700 hover:bg-slate-50 font-black text-sm transition" title="Zoom out">&minus;</button>
                </div>

                <!-- Top-Right: Realistic Map Layer Switcher (Satelit Asli / Peta Jalan / Topografi) -->
                <div class="absolute top-4 right-4 z-20 flex items-center bg-white/95 backdrop-blur-md rounded-xl p-1 shadow-lg border border-slate-200/80 text-[11px] font-bold">
                    <button type="button" id="layerSatelliteBtn" class="layer-btn px-2.5 py-1 rounded-lg bg-[#2479E8] text-white transition flex items-center gap-1 shadow-xs" title="Tampilan Satelit Asli Bumi (Google Hybrid)">
                        <span>🛰️</span>
                        <span>Satelit Asli</span>
                    </button>
                    <button type="button" id="layerStreetsBtn" class="layer-btn px-2.5 py-1 rounded-lg bg-transparent text-slate-700 hover:bg-slate-100 transition flex items-center gap-1" title="Tampilan Peta Jalan Navigasi">
                        <span>🗺️</span>
                        <span>Peta Jalan</span>
                    </button>
                    <button type="button" id="layerTerrainBtn" class="layer-btn px-2.5 py-1 rounded-lg bg-transparent text-slate-700 hover:bg-slate-100 transition flex items-center gap-1" title="Tampilan Topografi & Medan (Esri Topo)">
                        <span>⛰️</span>
                        <span>Topografi</span>
                    </button>
                </div>

                <!-- Top-Center: Live Shipment Location Header Badge -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-xs px-4 py-2 rounded-full shadow-lg border border-slate-200/80 z-20 flex flex-col items-center justify-center text-center pointer-events-none">
                    <div class="flex items-center gap-2">
                        <span id="liveHeaderBadge" class="flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-600 text-[10px] font-black tracking-wider uppercase">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                            LIVE
                        </span>
                        <span class="text-xs font-black text-slate-800 tracking-tight" id="liveHeaderTitle">LIVE SHIPMENT LOCATION</span>
                    </div>
                    <span class="text-[9.5px] text-slate-400 font-medium -mt-0.5" id="liveLastUpdated">Peta Asli &bull; Telematika Real-Time</span>
                </div>

                <!-- User Live GPS Status Pill (Floating below header when GPS is active) -->
                <div id="userGpsBanner" class="absolute top-16 left-1/2 -translate-x-1/2 bg-emerald-950/90 text-emerald-100 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-emerald-500/40 z-20 hidden items-center gap-2 text-[10px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>📍 GPS Anda: <span id="userGpsAcc" class="text-white font-mono">±12m</span></span>
                    <span class="text-emerald-400/50">&bull;</span>
                    <span id="userGpsDistanceTag" class="text-emerald-300">Menghitung jarak ke kargo...</span>
                </div>

                <!-- Bottom-Left: White Rounded Legend Box -->
                <div class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-xs px-3.5 py-2 rounded-2xl shadow-lg border border-slate-200/80 flex items-center gap-3 text-[11px] text-slate-700 font-bold z-20 flex-wrap">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#EF4444] shadow-xs"></span>
                        <span id="legendOriginCity">Origin</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2563EB] shadow-xs"></span>
                        <span>In Transit</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] shadow-xs"></span>
                        <span id="legendDestCity">Destination</span>
                    </span>
                    <span id="legendUserGpsItem" class="hidden items-center gap-1.5 text-emerald-700 font-bold border-l border-slate-200 pl-2.5">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span>Lokasi Anda</span>
                    </span>
                </div>

                <!-- Bottom-Right: Subtle GPS Coordinate Tag -->
                <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-xs px-3.5 py-2 rounded-xl shadow-lg border border-slate-200/80 text-[10px] font-mono font-bold text-slate-700 z-20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span id="floatingGpsTag">LAT: 10.4283° N &bull; LON: 76.5422° E</span>
                </div>

                <!-- Map Synchronizing Loader -->
                <div id="liveMapLoader" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-30 transition-opacity hidden">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xl border border-slate-700">
                        <svg class="animate-spin h-4 w-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Memuat Koordinat Geodesik...</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- ===================================================================== -->
        <!-- BOTTOM RIGHT: Recent Shipments (lg:col-span-5) -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-100/90 shadow-2xs p-5 flex flex-col justify-between">
            
            <!-- Recent Shipments Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Recent Shipments</h2>
                </div>

                <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition">
                    <span>View All</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Shipment List Items (4 Items with Interactive "Track on Map" Click) -->
            <div class="mt-4 space-y-3 relative">
                
                <!-- Vertical Timeline Track Line behind icons -->
                <div class="absolute left-3.5 top-3 bottom-3 w-0.5 bg-slate-100 z-0 pointer-events-none"></div>

                <!-- Shipment 1: CT-2026-SEA-8841 (In Transit) -->
                <div class="recent-shipment-row relative z-10 p-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer flex items-center justify-between text-xs group" data-shipment-num="CT-2026-SEA-8841">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#10B981] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition flex items-center gap-1.5 text-[13px]">
                                <span>CT-2026-SEA-8841</span>
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-blue-50 text-blue-600 font-bold uppercase tracking-wider">Active</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Jakarta &rarr; Rotterdam (N. TRANSIT)
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Oct 27, 2026 &bull; 14:30
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#EBF5FF] text-[#2563EB] border border-blue-100/70">
                        In Transit
                    </span>
                </div>

                <!-- Shipment 2: CT-2026-SEA-8840 (On Hold) -->
                <div class="recent-shipment-row relative z-10 p-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer flex items-center justify-between text-xs group" data-shipment-num="CT-2026-SEA-8840">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#2563EB] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition text-[13px]">
                                CT-2026-SEA-8840
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Jakarta &rarr; Singapore
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Oct 27, 2026 &bull; 11:20
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#FEF3C7] text-[#D97706] border border-amber-100/70">
                        On Hold
                    </span>
                </div>

                <!-- Shipment 3: CT-2026-SEA-8839 (Delivered) -->
                <div class="recent-shipment-row relative z-10 p-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer flex items-center justify-between text-xs group" data-shipment-num="CT-2026-SEA-8839">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#10B981] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition text-[13px]">
                                CT-2026-SEA-8839
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Jakarta &rarr; Tokyo
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Oct 26, 2026 &bull; 16:45
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#DCFCE7] text-[#16A34A] border border-emerald-100/70">
                        Delivered
                    </span>
                </div>

                <!-- Shipment 4: CT-2026-SEA-8838 (Exception) -->
                <div class="recent-shipment-row relative z-10 p-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer flex items-center justify-between text-xs group" data-shipment-num="CT-2026-SEA-8838">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#EF4444] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition text-[13px]">
                                CT-2026-SEA-8838
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Jakarta &rarr; Sydney
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Oct 26, 2026 &bull; 13:10
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#FEE2E2] text-[#DC2626] border border-rose-100/70">
                        Exception
                    </span>
                </div>

            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. EXTENDED ANALYTICS & INCIDENT CLEARANCE CONSOLE (Collapsible) -->
    <!-- ========================================================================= -->
    <div class="mt-8 pt-4 border-t border-slate-200/80">
        
        <!-- Collapsible Trigger Button -->
        <button type="button" id="toggleTelematicsBtn" class="w-full flex items-center justify-between p-4 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl shadow-2xs text-left transition group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:scale-105 transition">
                    📊
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Operational Analytics &amp; Incident Mitigation Console</h3>
                    <p class="text-[11px] text-slate-400">Chart.js lifecycle distribution, carrier line volumes, and active incident mitigation.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-blue-600">
                <span id="telematicsToggleText">Show Analytics</span>
                <svg id="telematicsChevron" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </button>

        <!-- Collapsible Content -->
        <div id="telematicsContent" class="hidden mt-6 space-y-6">

            <!-- Chart.js Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Status Distribution -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Shipment Status Distribution</h3>
                    <div class="relative h-56 flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Carrier Line Distribution -->
                <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Shipment Volume by Carrier Line</h3>
                    <div class="relative h-56">
                        <canvas id="carrierChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Active Exceptions & Incident Table -->
            <div class="bg-white rounded-2xl border border-rose-100 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-rose-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        Active Incidents Requiring Mitigation
                    </h3>
                    <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                        {{ $activeExceptions->count() }} Open
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($activeExceptions as $exc)
                        <div class="p-3.5 rounded-xl bg-rose-50/50 border border-rose-100 text-xs flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between font-bold text-rose-900">
                                    <span>{{ $exc->exception_type }}</span>
                                    <span class="text-[9px] px-2 py-0.5 rounded uppercase {{ $exc->severity === 'CRITICAL' ? 'bg-rose-600 text-white' : 'bg-rose-200 text-rose-800' }}">
                                        {{ $exc->severity }}
                                    </span>
                                </div>
                                <p class="text-slate-600 mt-1 line-clamp-2">{{ $exc->description }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-rose-100 text-[11px]">
                                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="font-bold text-blue-600 hover:underline">
                                    {{ $exc->shipment->shipment_number }} &bull; +{{ $exc->delay_impact_hours }}h impact
                                </a>
                                @if(Auth::user()->canManageShipments())
                                    <form method="POST" action="{{ route('exceptions.resolve', $exc->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-[10px] font-bold px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                                            Resolve
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-6 text-center text-xs text-slate-400">
                            ✨ All shipments operating with zero active exceptions.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================================================
    // 1. LIVE FLIGHT TRACKING ENGINE & GEODESIC SIMULATOR
    // =========================================================================
    const mapContainer = document.getElementById('liveShipmentMap');
    if (!mapContainer || typeof L === 'undefined') return;

    // Initialize Leaflet Map centered between Europe and Southeast Asia
    const liveMap = L.map('liveShipmentMap', {
        zoomControl: false, // Custom zoom buttons used
        scrollWheelZoom: false,
        attributionControl: false
    }).setView([20, 55], 3);

    // 1. REALISTIC MAP TILES (Google Hybrid Satelit, Google Streets, Esri Topo, OSM Fallbacks)
    // Google Hybrid: Real High-Definition Earth Satellite Photography with Country, Ocean & Road Labels
    const googleHybridTile = L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['0', '1', '2', '3'],
        attribution: '&copy; Google Maps'
    });

    // Google Streets: Detailed Street & Highway Navigation
    const googleStreetsTile = L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['0', '1', '2', '3'],
        attribution: '&copy; Google Maps'
    });

    // Esri Topo: Topography & Terrain
    const esriTopoTile = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri'
    });

    // Resilient Fallback Tile Layers
    const esriSatTile = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });
    const osmTile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        subdomains: 'abc',
        attribution: '&copy; OpenStreetMap contributors'
    });

    // Default: Google Hybrid (Satelit Asli) for authentic realistic world view
    let currentTileLayer = googleHybridTile;
    googleHybridTile.addTo(liveMap);

    // Fallbacks if Google tiles are unreachable
    googleHybridTile.on('tileerror', function () {
        if (currentTileLayer === googleHybridTile) {
            console.warn('Google Hybrid unavailable, switching to Esri World Imagery fallback.');
            liveMap.removeLayer(googleHybridTile);
            currentTileLayer = esriSatTile;
            esriSatTile.addTo(liveMap);
        }
    });
    googleStreetsTile.on('tileerror', function () {
        if (currentTileLayer === googleStreetsTile) {
            console.warn('Google Streets unavailable, switching to OpenStreetMap fallback.');
            liveMap.removeLayer(googleStreetsTile);
            currentTileLayer = osmTile;
            osmTile.addTo(liveMap);
        }
    });

    // Realistic Layer Switcher Handler
    function setMapLayer(type) {
        if (currentTileLayer) liveMap.removeLayer(currentTileLayer);

        document.querySelectorAll('.layer-btn').forEach(b => {
            b.classList.remove('bg-[#2479E8]', 'text-white', 'shadow-xs');
            b.classList.add('bg-transparent', 'text-slate-700');
        });

        if (type === 'satellite') {
            currentTileLayer = googleHybridTile;
            const btn = document.getElementById('layerSatelliteBtn');
            if (btn) {
                btn.classList.remove('bg-transparent', 'text-slate-700');
                btn.classList.add('bg-[#2479E8]', 'text-white', 'shadow-xs');
            }
        } else if (type === 'streets') {
            currentTileLayer = googleStreetsTile;
            const btn = document.getElementById('layerStreetsBtn');
            if (btn) {
                btn.classList.remove('bg-transparent', 'text-slate-700');
                btn.classList.add('bg-[#2479E8]', 'text-white', 'shadow-xs');
            }
        } else if (type === 'terrain') {
            currentTileLayer = esriTopoTile;
            const btn = document.getElementById('layerTerrainBtn');
            if (btn) {
                btn.classList.remove('bg-transparent', 'text-slate-700');
                btn.classList.add('bg-[#2479E8]', 'text-white', 'shadow-xs');
            }
        }
        currentTileLayer.addTo(liveMap);
    }

    document.getElementById('layerSatelliteBtn')?.addEventListener('click', () => setMapLayer('satellite'));
    document.getElementById('layerStreetsBtn')?.addEventListener('click', () => setMapLayer('streets'));
    document.getElementById('layerTerrainBtn')?.addEventListener('click', () => setMapLayer('terrain'));

    // Custom Zoom Button Handlers
    document.getElementById('mapZoomIn')?.addEventListener('click', () => liveMap.zoomIn());
    document.getElementById('mapZoomOut')?.addEventListener('click', () => liveMap.zoomOut());

    // Layer Groups
    const markersLayer = L.layerGroup().addTo(liveMap);
    let remainingPolyline = null;
    let traversedPolyline = null;
    let planeMarker = null;

    // Animation & Telemetry State
    let animationFrameId = null;
    let isPlaying = true;
    let autoFollow = true;
    let speedMultiplier = 2; // Default 2x
    let flightProgress = 0.0; // 0.0 to 1.0
    let lastTimestamp = null;
    let flightDurationMs = 40000; // 40s base flight duration
    let fullFlightPath = []; // Array of [lat, lng]
    let activeShipmentData = null;

    // Live User GPS State
    let userWatchId = null;
    let userMarker = null;
    let userAccuracyCircle = null;
    let userCargoConnector = null;
    let userCurrentCoords = null; // [lat, lng]
    let cameraMode = 'cargo'; // 'cargo' | 'user' | 'both'
    let demoInterval = null;

    // Great-Circle Haversine Distance (KM)
    function haversineDistanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    // Geodesic (Great-Circle) Path Generator
    function toRad(deg) { return deg * Math.PI / 180; }
    function toDeg(rad) { return rad * 180 / Math.PI; }

    function interpolateGreatCircle(lat1, lon1, lat2, lon2, steps = 80) {
        const phi1 = toRad(lat1), lam1 = toRad(lon1);
        const phi2 = toRad(lat2), lam2 = toRad(lon2);
        
        const d = 2 * Math.asin(Math.sqrt(
            Math.pow(Math.sin((phi2 - phi1) / 2), 2) +
            Math.cos(phi1) * Math.cos(phi2) * Math.pow(Math.sin((lam2 - lam1) / 2), 2)
        ));

        if (d < 1e-6) return [[lat1, lon1]];

        const points = [];
        for (let i = 0; i <= steps; i++) {
            const f = i / steps;
            const A = Math.sin((1 - f) * d) / Math.sin(d);
            const B = Math.sin(f * d) / Math.sin(d);
            
            const x = A * Math.cos(phi1) * Math.cos(lam1) + B * Math.cos(phi2) * Math.cos(lam2);
            const y = A * Math.cos(phi1) * Math.sin(lam1) + B * Math.cos(phi2) * Math.sin(lam2);
            const z = A * Math.sin(phi1) + B * Math.sin(phi2);
            
            const phi = Math.atan2(z, Math.sqrt(x * x + y * y));
            const lam = Math.atan2(y, x);
            points.push([toDeg(phi), toDeg(lam)]);
        }
        return points;
    }

    // Custom Leaflet Icons:
    // Origin Marker (Red with white inner dot)
    function createOriginMarker(city) {
        return L.divIcon({
            className: 'custom-origin-marker',
            html: `
                <div style="position: relative; width: 0; height: 0;">
                    <div style="position: absolute; transform: translate(-50%, -100%); display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                        <div style="background: #EF4444; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 11px; border: 2.5px solid white; box-shadow: 0 4px 10px rgba(239,68,68,0.5);">
                            O
                        </div>
                        <div style="background: rgba(15,23,42,0.92); border: 1px solid #334155; padding: 2px 7px; border-radius: 8px; font-size: 9px; font-weight: 800; color: #FFFFFF; margin-top: 3px; white-space: nowrap; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                            ${city.toUpperCase()}
                        </div>
                    </div>
                </div>
            `,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });
    }

    // Destination Marker with Concentric Radar Pulse Waves
    function createDestinationMarker(city) {
        return L.divIcon({
            className: 'custom-dest-marker',
            html: `
                <div style="position: relative; width: 0; height: 0; display: flex; align-items: center; justify-content: center;">
                    <!-- Concentric Translucent Blue/Green Radar Pulse Rings -->
                    <div class="animate-radar-1" style="position: absolute; left: -22px; top: -22px; width: 44px; height: 44px; border-radius: 50%; border: 2px solid rgba(16, 185, 129, 0.65); pointer-events: none;"></div>
                    <div class="animate-radar-2" style="position: absolute; left: -34px; top: -34px; width: 68px; height: 68px; border-radius: 50%; border: 1.5px solid rgba(37, 99, 235, 0.45); pointer-events: none;"></div>
                    <div class="animate-radar-3" style="position: absolute; left: -46px; top: -46px; width: 92px; height: 92px; border-radius: 50%; border: 1px solid rgba(37, 99, 235, 0.3); pointer-events: none;"></div>

                    <!-- Location Pin -->
                    <div style="position: absolute; transform: translate(-50%, -100%); display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                        <div style="background: #10B981; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 11px; border: 2.5px solid white; box-shadow: 0 4px 10px rgba(16,185,129,0.5);">
                            D
                        </div>
                        <div style="background: rgba(15,23,42,0.92); border: 1px solid #334155; padding: 2px 7px; border-radius: 8px; font-size: 9px; font-weight: 800; color: #FFFFFF; margin-top: 3px; white-space: nowrap; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                            ${city.toUpperCase()}
                        </div>
                    </div>
                </div>
            `,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });
    }

    // Transit Checkpoint Marker
    function createTransitMarker(name) {
        return L.divIcon({
            className: 'custom-transit-marker',
            html: `
                <div style="position: relative; width: 0; height: 0;">
                    <div style="position: absolute; transform: translate(-50%, -100%); display: flex; flex-direction: column; align-items: center;">
                        <div style="background: #2563EB; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 10px; border: 2px solid white; box-shadow: 0 2px 6px rgba(37,99,235,0.5);">
                            &bull;
                        </div>
                        <div style="background: rgba(15,23,42,0.85); border: 1px solid #334155; padding: 1px 5px; border-radius: 6px; font-size: 8px; font-weight: 700; color: #E2E8F0; margin-top: 2px; white-space: nowrap;">
                            ${name}
                        </div>
                    </div>
                </div>
            `,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });
    }

    // User Location Marker Icon (Pulsing Radar Pin for Live GPS Device)
    function createUserMarkerIcon() {
        return L.divIcon({
            className: 'custom-user-gps-marker',
            html: `
                <div style="position: relative; width: 0; height: 0; display: flex; align-items: center; justify-content: center;">
                    <!-- Concentric Radar Pulses -->
                    <div class="animate-user-gps" style="position: absolute; left: -24px; top: -24px; width: 48px; height: 48px; border-radius: 50%; border: 2.5px solid rgba(16, 185, 129, 0.8); background: rgba(16, 185, 129, 0.15); pointer-events: none;"></div>
                    <div class="animate-radar-2" style="position: absolute; left: -36px; top: -36px; width: 72px; height: 72px; border-radius: 50%; border: 1.5px solid rgba(59, 130, 246, 0.5); pointer-events: none;"></div>

                    <!-- Glowing Emerald Navigation Beacon Pin -->
                    <div style="position: absolute; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; cursor: pointer; z-index: 60;">
                        <div style="background: #10B981; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 2.5px solid #FFFFFF; box-shadow: 0 4px 14px rgba(16,185,129,0.7);">
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.8">
                                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3m10-10h-3M5 12H2"/>
                            </svg>
                        </div>
                        <div style="background: #0B172E; color: white; border: 1px solid #1E293B; padding: 2px 8px; border-radius: 9999px; font-size: 9px; font-weight: 800; margin-top: 4px; white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 4px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                            LOKASI SAYA
                        </div>
                    </div>
                </div>
            `,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });
    }

    // Airplane Icon with Concentric Radar Pulse Rings + Floating Telemetry Tooltip
    const planeIcon = L.divIcon({
        className: 'custom-live-plane-marker',
        html: `
            <div style="position: relative; width: 0; height: 0;">
                <!-- Concentric Translucent Blue Radar Pulse Rings -->
                <div class="animate-radar-1" style="position: absolute; left: -22px; top: -22px; width: 44px; height: 44px; border-radius: 50%; border: 2px solid rgba(56, 189, 248, 0.7); pointer-events: none;"></div>
                <div class="animate-radar-2" style="position: absolute; left: -34px; top: -34px; width: 68px; height: 68px; border-radius: 50%; border: 1.5px solid rgba(56, 189, 248, 0.45); pointer-events: none;"></div>
                <div class="animate-radar-3" style="position: absolute; left: -46px; top: -46px; width: 92px; height: 92px; border-radius: 50%; border: 1px solid rgba(56, 189, 248, 0.25); pointer-events: none;"></div>

                <!-- Airplane Rotator Disc -->
                <div id="livePlaneRotator" style="position: absolute; left: -19px; top: -19px; width: 38px; height: 38px; border-radius: 50%; background: #0F224A; display: flex; align-items: center; justify-content: center; border: 2.5px solid #FFFFFF; box-shadow: 0 4px 14px rgba(15,34,74,0.65); cursor: pointer; transition: transform 0.08s linear; z-index: 50;">
                    <svg style="width: 20px; height: 20px; color: #FFFFFF;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>

                <!-- Floating Live Flight Telemetry Tooltip pinned next to aircraft -->
                <div class="telemetry-tooltip-box" style="position: absolute; left: 24px; top: -17px; white-space: nowrap; pointer-events: none; z-index: 60;">
                    <div style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(6px); padding: 5px 12px; border-radius: 9999px; border: 1px solid #334155; box-shadow: 0 4px 14px rgba(0,0,0,0.3); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; gap: 6px; font-size: 11px;">
                        <span style="font-weight: 800; color: #FFFFFF;" id="tooltipFlightNumber">Flight CT-2026</span>
                        <span style="color: #64748B;">&bull;</span>
                        <span style="color: #CBD5E1;" id="tooltipSpeed">Speed: 840 km/h</span>
                        <span style="color: #64748B;">&bull;</span>
                        <span style="color: #CBD5E1;">Alt: 35,000 ft</span>
                        <span style="color: #64748B;">&bull;</span>
                        <span style="font-weight: 800; color: #38BDF8; background: rgba(56,189,248,0.15); padding: 1px 7px; border-radius: 9999px; font-size: 10px;" id="tooltipStatus">EN ROUTE</span>
                    </div>
                </div>
            </div>
        `,
        iconSize: [0, 0],
        iconAnchor: [0, 0]
    });

    // Handle User Location Updates (Real-Time GPS Tracking)
    function onUserLocationUpdate(lat, lng, accuracy, speed) {
        userCurrentCoords = [lat, lng];

        const latStr = `${Math.abs(lat).toFixed(4)}° ${lat >= 0 ? 'N' : 'S'}`;
        const lngStr = `${Math.abs(lng).toFixed(4)}° ${lng >= 0 ? 'E' : 'W'}`;

        // Show banner & indicators
        const banner = document.getElementById('userGpsBanner');
        if (banner) {
            banner.classList.remove('hidden');
            banner.classList.add('flex');
        }
        const accEl = document.getElementById('userGpsAcc');
        if (accEl) accEl.innerText = `±${Math.round(accuracy)}m`;

        const legendGps = document.getElementById('legendUserGpsItem');
        if (legendGps) {
            legendGps.classList.remove('hidden');
            legendGps.classList.add('flex');
        }

        const focusGroup = document.getElementById('cameraFocusGroup');
        if (focusGroup) {
            focusGroup.classList.remove('hidden');
            focusGroup.classList.add('flex');
        }

        // Create or update User Marker
        if (!userMarker) {
            userMarker = L.marker([lat, lng], {
                icon: createUserMarkerIcon(),
                zIndexOffset: 4000
            }).addTo(liveMap);

            userAccuracyCircle = L.circle([lat, lng], {
                radius: Math.max(accuracy, 25),
                color: '#10B981',
                fillColor: '#10B981',
                fillOpacity: 0.15,
                weight: 1.5
            }).addTo(liveMap);

            // Fly camera to user location on initial fix
            cameraMode = 'user';
            setCameraFocusUI('user');
            liveMap.flyTo([lat, lng], 14, { duration: 1.4 });
        } else {
            userMarker.setLatLng([lat, lng]);
            if (userAccuracyCircle) {
                userAccuracyCircle.setLatLng([lat, lng]);
                userAccuracyCircle.setRadius(Math.max(accuracy, 25));
            }
        }

        // Calculate distance to current shipment cargo position
        let distCargo = null;
        let distDest = null;
        if (planeMarker) {
            const cPos = planeMarker.getLatLng();
            distCargo = haversineDistanceKm(lat, lng, cPos.lat, cPos.lng);
        }
        if (activeShipmentData?.destination) {
            distDest = haversineDistanceKm(lat, lng, activeShipmentData.destination.lat, activeShipmentData.destination.lng);
        }

        const distTag = document.getElementById('userGpsDistanceTag');
        if (distTag) {
            if (distCargo !== null) {
                distTag.innerText = `Jarak ke Kargo: ${distCargo.toLocaleString()} km`;
            } else if (distDest !== null) {
                distTag.innerText = `Jarak ke Tujuan: ${distDest.toLocaleString()} km`;
            }
        }

        // Draw / update tactical dashed connector line between User GPS and Cargo
        if (planeMarker) {
            const cPos = planeMarker.getLatLng();
            if (!userCargoConnector) {
                userCargoConnector = L.polyline([[lat, lng], [cPos.lat, cPos.lng]], {
                    color: '#34D399',
                    weight: 2,
                    dashArray: '5, 5',
                    opacity: 0.85
                }).addTo(liveMap);
            } else {
                userCargoConnector.setLatLngs([[lat, lng], [cPos.lat, cPos.lng]]);
            }
        }

        // User Marker Interactive Popup
        userMarker.bindPopup(`
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12px; min-width: 230px; padding: 4px;">
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px; margin-bottom: 8px;">
                    <strong style="color: #059669; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                        <span>📍</span> Lokasi Anda Saat Ini
                    </strong>
                    <span style="font-size: 9px; font-weight: 800; background: #DCFCE7; color: #15803D; padding: 1px 6px; border-radius: 9999px;">LIVE GPS</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px; color: #475569;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Koordinat:</span>
                        <strong style="color: #0F172A; font-family: monospace;">${latStr}, ${lngStr}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Akurasi GPS:</span>
                        <strong style="color: #0F172A;">±${Math.round(accuracy)} meter</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Kecepatan:</span>
                        <strong style="color: #0F172A;">${speed ? speed + ' km/h' : 'Diam / Stasioner'}</strong>
                    </div>
                    ${distCargo !== null ? `
                    <div style="display: flex; justify-content: space-between; margin-top: 4px; padding-top: 4px; border-top: 1px dashed #CBD5E1;">
                        <span style="color: #2563EB; font-weight: bold;">✈️ Jarak ke Kargo:</span>
                        <strong style="color: #2563EB;">${distCargo.toLocaleString()} km</strong>
                    </div>
                    ` : ''}
                    ${distDest !== null ? `
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #059669; font-weight: bold;">🏁 Jarak ke Tujuan:</span>
                        <strong style="color: #059669;">${distDest.toLocaleString()} km</strong>
                    </div>
                    ` : ''}
                </div>
            </div>
        `);

        // Camera Follow
        if (cameraMode === 'user') {
            liveMap.panTo([lat, lng], { animate: false });
        } else if (cameraMode === 'both' && planeMarker) {
            const cPos = planeMarker.getLatLng();
            liveMap.fitBounds(L.latLngBounds([[lat, lng], [cPos.lat, cPos.lng]]), { padding: [60, 60], maxZoom: 14 });
        }
    }

    // Start Live Geolocation Watch
    function startUserLiveLocation() {
        const btn = document.getElementById('liveUserGpsBtn');
        const text = document.getElementById('userGpsText');
        const ping = document.getElementById('userGpsPing');
        const dot = document.getElementById('userGpsDot');

        if (text) text.innerText = 'Mencari GPS...';
        if (ping) ping.classList.remove('hidden');

        if (!navigator.geolocation) {
            showGpsFallbackModal('Browser ini tidak mendukung Geolocation GPS.');
            return;
        }

        userWatchId = navigator.geolocation.watchPosition(
            (pos) => {
                if (text) text.innerText = 'GPS: Aktif';
                if (btn) {
                    btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
                    btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-700', 'shadow-md');
                }
                if (dot) dot.classList.replace('bg-emerald-600', 'bg-white');

                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const acc = pos.coords.accuracy || 20;
                const spd = pos.coords.speed ? Math.round(pos.coords.speed * 3.6) : null;
                onUserLocationUpdate(lat, lng, acc, spd);
            },
            (err) => {
                console.warn('GPS Geolocation watch failed:', err);
                showGpsFallbackModal(err.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 3000
            }
        );
    }

    // Stop User Live Geolocation
    function stopUserLiveLocation() {
        if (userWatchId !== null) {
            navigator.geolocation.clearWatch(userWatchId);
            userWatchId = null;
        }
        if (demoInterval !== null) {
            clearInterval(demoInterval);
            demoInterval = null;
        }

        const btn = document.getElementById('liveUserGpsBtn');
        const text = document.getElementById('userGpsText');
        const ping = document.getElementById('userGpsPing');
        const dot = document.getElementById('userGpsDot');
        const banner = document.getElementById('userGpsBanner');
        const legendGps = document.getElementById('legendUserGpsItem');
        const focusGroup = document.getElementById('cameraFocusGroup');

        if (btn) {
            btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-700', 'shadow-md');
            btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
        }
        if (text) text.innerText = 'Live Lokasi Saya';
        if (dot) dot.classList.replace('bg-white', 'bg-emerald-600');
        if (ping) ping.classList.add('hidden');
        if (banner) banner.classList.add('hidden');
        if (legendGps) legendGps.classList.add('hidden');
        if (focusGroup) focusGroup.classList.add('hidden');

        if (userMarker) { liveMap.removeLayer(userMarker); userMarker = null; }
        if (userAccuracyCircle) { liveMap.removeLayer(userAccuracyCircle); userAccuracyCircle = null; }
        if (userCargoConnector) { liveMap.removeLayer(userCargoConnector); userCargoConnector = null; }

        userCurrentCoords = null;
        cameraMode = 'cargo';
    }

    function toggleUserLiveLocation() {
        if (userWatchId !== null || demoInterval !== null) {
            stopUserLiveLocation();
        } else {
            startUserLiveLocation();
        }
    }

    // Simulated GPS in Jakarta (Demo Mode fallback)
    function startDemoGps() {
        stopUserLiveLocation();
        const btn = document.getElementById('liveUserGpsBtn');
        const text = document.getElementById('userGpsText');
        const ping = document.getElementById('userGpsPing');
        const dot = document.getElementById('userGpsDot');

        if (btn) {
            btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
            btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-700', 'shadow-md');
        }
        if (text) text.innerText = 'GPS Demo (Jakarta)';
        if (dot) dot.classList.replace('bg-emerald-600', 'bg-white');
        if (ping) ping.classList.remove('hidden');

        let demoLat = -6.1214;
        let demoLng = 106.8822;
        let step = 0;

        onUserLocationUpdate(demoLat, demoLng, 18, 28);

        demoInterval = setInterval(() => {
            step++;
            demoLat += (Math.sin(step / 3) * 0.0006);
            demoLng += (Math.cos(step / 3) * 0.0006);
            onUserLocationUpdate(demoLat, demoLng, 15 + Math.round(Math.random() * 8), 32);
        }, 2200);
    }

    function showGpsFallbackModal(reason) {
        const existing = document.getElementById('gpsFallbackAlert');
        if (existing) existing.remove();

        const alertDiv = document.createElement('div');
        alertDiv.id = 'gpsFallbackAlert';
        alertDiv.className = 'fixed bottom-6 right-6 max-w-sm bg-slate-900/95 text-white p-4 rounded-2xl shadow-2xl border border-slate-700 z-50 flex flex-col gap-2.5 backdrop-blur-md';
        alertDiv.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📍</span>
                    <div>
                        <h4 class="font-bold text-xs text-white">Izin Lokasi Browser</h4>
                        <p class="text-[11px] text-slate-300 mt-0.5">Izin GPS perangkat belum aktif atau diblokir di browser Anda.</p>
                    </div>
                </div>
                <button type="button" onclick="this.closest('#gpsFallbackAlert').remove()" class="text-slate-400 hover:text-white text-sm font-bold ml-2">&times;</button>
            </div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-800">
                <button type="button" id="btnRunDemoGps" class="w-full py-1.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-xs transition">
                    Gunakan Lokasi Demo (Jakarta)
                </button>
                <button type="button" onclick="this.closest('#gpsFallbackAlert').remove()" class="py-1.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition">
                    Tutup
                </button>
            </div>
        `;
        document.body.appendChild(alertDiv);

        document.getElementById('btnRunDemoGps')?.addEventListener('click', () => {
            alertDiv.remove();
            startDemoGps();
        });
    }

    // Camera Focus Modes
    function setCameraFocusUI(mode) {
        cameraMode = mode;
        const cargoBtn = document.getElementById('focusCargoBtn');
        const userBtn = document.getElementById('focusUserBtn');
        const allBtn = document.getElementById('focusAllBtn');

        [cargoBtn, userBtn, allBtn].forEach(b => {
            b?.classList.remove('bg-[#2479E8]', 'text-white');
            b?.classList.add('bg-white', 'text-slate-600');
        });

        if (mode === 'cargo') {
            cargoBtn?.classList.replace('bg-white', 'bg-[#2479E8]');
            cargoBtn?.classList.replace('text-slate-600', 'text-white');
            if (planeMarker) liveMap.panTo(planeMarker.getLatLng(), { animate: true });
        } else if (mode === 'user') {
            userBtn?.classList.replace('bg-white', 'bg-[#2479E8]');
            userBtn?.classList.replace('text-slate-600', 'text-white');
            if (userCurrentCoords) liveMap.flyTo(userCurrentCoords, 14, { duration: 1.2 });
        } else if (mode === 'both') {
            allBtn?.classList.replace('bg-white', 'bg-[#2479E8]');
            allBtn?.classList.replace('text-slate-600', 'text-white');
            if (userCurrentCoords && planeMarker) {
                const cPos = planeMarker.getLatLng();
                liveMap.fitBounds(L.latLngBounds([userCurrentCoords, [cPos.lat, cPos.lng]]), { padding: [60, 60] });
            }
        }
    }

    document.getElementById('liveUserGpsBtn')?.addEventListener('click', toggleUserLiveLocation);
    document.getElementById('focusCargoBtn')?.addEventListener('click', () => setCameraFocusUI('cargo'));
    document.getElementById('focusUserBtn')?.addEventListener('click', () => setCameraFocusUI('user'));
    document.getElementById('focusAllBtn')?.addEventListener('click', () => setCameraFocusUI('both'));

    // Fetch and Load Shipment Data
    async function loadShipmentRoute(shipmentId) {
        if (!shipmentId) return;

        const loader = document.getElementById('liveMapLoader');
        if (loader) loader.classList.remove('hidden');

        try {
            const response = await fetch(`/shipments/${shipmentId}/coordinates`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network error');
            const result = await response.json();
            if (!result.success || !result.data) throw new Error('Invalid data');

            activeShipmentData = result.data;
            setupFlightCorridor(activeShipmentData);
        } catch (err) {
            console.error('Failed to load shipment coordinates:', err);
            setupDefaultDemoRoute();
        } finally {
            if (loader) loader.classList.add('hidden');
        }
    }

    // Setup Geodesic Flight Corridor from Data
    function setupFlightCorridor(d) {
        // Clear previous layers
        markersLayer.clearLayers();
        if (remainingPolyline) { liveMap.removeLayer(remainingPolyline); remainingPolyline = null; }
        if (traversedPolyline) { liveMap.removeLayer(traversedPolyline); traversedPolyline = null; }
        if (planeMarker) { liveMap.removeLayer(planeMarker); planeMarker = null; }

        const origin = d.origin;
        const dest = d.destination;
        if (!origin || !dest) return;

        // Build Multi-segment Geodesic Corridor
        fullFlightPath = [];
        const waypoints = [[origin.lat, origin.lng]];

        // Add intermediate checkpoints if available
        if (d.events && d.events.length > 0) {
            d.events.forEach(evt => {
                if (evt.lat && evt.lng && 
                    (Math.abs(evt.lat - origin.lat) > 0.1 || Math.abs(evt.lng - origin.lng) > 0.1) &&
                    (Math.abs(evt.lat - dest.lat) > 0.1 || Math.abs(evt.lng - dest.lng) > 0.1)) {
                    waypoints.push([evt.lat, evt.lng]);
                    // Plot waypoint marker
                    L.marker([evt.lat, evt.lng], {
                        icon: createTransitMarker(evt.location_name ? evt.location_name.split(' ')[0] : 'Transit')
                    }).addTo(markersLayer).bindPopup(`<strong>Checkpoint:</strong> ${evt.location_name}<br/>${evt.description}`);
                }
            });
        }
        waypoints.push([dest.lat, dest.lng]);

        // Interpolate smooth great-circle curves between consecutive waypoints
        for (let i = 0; i < waypoints.length - 1; i++) {
            const seg = interpolateGreatCircle(waypoints[i][0], waypoints[i][1], waypoints[i+1][0], waypoints[i+1][1], 80);
            if (i > 0) seg.shift(); // prevent duplicate joint
            fullFlightPath = fullFlightPath.concat(seg);
        }

        // Plot Origin Terminal Marker
        L.marker([origin.lat, origin.lng], {
            icon: createOriginMarker(origin.city || 'Origin')
        }).addTo(markersLayer).bindPopup(`<strong>ORIGIN:</strong> ${origin.name} (${origin.city}, ${origin.country})`);

        // Plot Destination Terminal Marker with Radar Signal
        L.marker([dest.lat, dest.lng], {
            icon: createDestinationMarker(dest.city || 'Destination')
        }).addTo(markersLayer).bindPopup(`<strong>DESTINATION:</strong> ${dest.name} (${dest.city}, ${dest.country})`);

        // 1. Dotted Remaining Flight Polyline (Bright Sky Blue Dashed Line - High Contrast on Satellite & Streets)
        remainingPolyline = L.polyline(fullFlightPath, {
            color: '#38BDF8',
            weight: 3,
            dashArray: '6, 6',
            opacity: 0.9
        }).addTo(liveMap);

        // 2. Traversed Flight Polyline (Glowing Emerald Green Line)
        traversedPolyline = L.polyline([], {
            color: '#10B981',
            weight: 3.5,
            opacity: 0.95
        }).addTo(liveMap);

        // 3. Create Airplane Marker at Start
        const startPoint = fullFlightPath[0];
        planeMarker = L.marker(startPoint, {
            icon: planeIcon,
            zIndexOffset: 3000
        }).addTo(liveMap);

        // Interactive Popup on Plane Click (Requirement #4)
        planeMarker.bindPopup(`
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12px; min-width: 220px; padding: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px; margin-bottom: 8px;">
                    <div>
                        <span style="font-size: 9px; font-weight: 800; color: #2563EB; text-transform: uppercase;">Live Telematics Fix</span>
                        <div style="font-weight: 900; font-size: 13px; color: #0F172A;" id="popShipmentNum">${d.shipment_number}</div>
                    </div>
                    <span style="font-size: 9px; font-weight: 800; background: #EFF6FF; color: #2563EB; padding: 2px 8px; border-radius: 9999px; border: 1px solid #BFDBFE;">${d.status || 'EN ROUTE'}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px; color: #475569;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">GPS Position:</span>
                        <strong style="color: #0F172A; font-family: monospace;" id="popCoords">--</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Airspace Sector:</span>
                        <strong style="color: #0F172A; text-align: right; max-width: 130px;" class="truncate" id="popSector">En Route</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Ground Speed:</span>
                        <strong style="color: #0F172A;" id="popSpeed">840 km/h</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Flight Progress:</span>
                        <strong style="color: #2479E8;" id="popProgress">0.0%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94A3B8;">Estimated Arrival:</span>
                        <strong style="color: #10B981;" id="popEta">${d.estimated_arrival || d.expected_arrival || '--'}</strong>
                    </div>
                </div>
            </div>
        `);

        // Fit map bounds to encompass route comfortably
        liveMap.fitBounds(remainingPolyline.getBounds(), {
            padding: [50, 50],
            maxZoom: 6
        });

        // Update static UI labels
        document.getElementById('quickAirwayText').innerText = `${origin.city} → ${dest.city}`;
        document.getElementById('legendOriginCity').innerText = `🔴 Origin (${origin.city})`;
        document.getElementById('legendDestCity').innerText = `🟢 Destination (${dest.city})`;
        document.getElementById('tooltipFlightNumber').innerText = `Flight ${d.shipment_number}`;

        // Reset flight position and restart animation
        flightProgress = 0.0;
        lastTimestamp = performance.now();
        if (!animationFrameId) {
            animationFrameId = requestAnimationFrame(animateFlight);
        }
    }

    // Default Rotterdam route fallback
    function setupDefaultDemoRoute() {
        setupFlightCorridor({
            shipment_number: 'CT-2026-SEA-8841',
            status: 'EN ROUTE',
            carrier: 'Maersk Line A/S',
            origin: { name: 'Port of Tanjung Priok', city: 'Jakarta', country: 'Indonesia', lat: -6.1033, lng: 106.8833 },
            destination: { name: 'Port of Rotterdam', city: 'Rotterdam', country: 'Netherlands', lat: 51.9054, lng: 4.4666 },
            events: [
                { location_name: 'Singapore PSA', lat: 1.2903, lng: 103.8520, description: 'Transshipment Hub' }
            ],
            estimated_arrival: 'Oct 28, 2026 18:00'
        });
    }

    // Smooth Continuous Animation Tick
    function animateFlight(now) {
        if (!lastTimestamp) lastTimestamp = now;
        const dt = (now - lastTimestamp) / 1000;
        lastTimestamp = now;

        if (isPlaying && fullFlightPath.length > 1) {
            // Advance progress smoothly based on speed multiplier
            const step = (dt / (flightDurationMs / 1000)) * speedMultiplier;
            flightProgress += step;

            // Loop after holding at destination
            if (flightProgress >= 1.0) {
                flightProgress = 1.0;
                setTimeout(() => {
                    if (flightProgress >= 1.0) flightProgress = 0.0;
                }, 2000);
            }

            // Interpolate position along geodesic path
            const totalSteps = fullFlightPath.length - 1;
            const floatIdx = flightProgress * totalSteps;
            const curIdx = Math.min(Math.floor(floatIdx), totalSteps - 1);
            const alpha = floatIdx - curIdx;

            const p1 = fullFlightPath[curIdx];
            const p2 = fullFlightPath[curIdx + 1] || p1;

            const curLat = p1[0] + alpha * (p2[0] - p1[0]);
            const curLng = p1[1] + alpha * (p2[1] - p1[1]);

            // Update airplane marker position
            if (planeMarker) {
                planeMarker.setLatLng([curLat, curLng]);

                // Calculate screen-space heading / rotation (tangent of polyline)
                const pt1 = liveMap.latLngToContainerPoint(p1);
                const pt2 = liveMap.latLngToContainerPoint(p2);
                const dx = pt2.x - pt1.x;
                const dy = pt2.y - pt1.y;
                const rad = Math.atan2(dy, dx);
                const screenAngle = (rad * 180 / Math.PI) + 90; // +90 because airplane SVG points UP

                const rotator = document.getElementById('livePlaneRotator');
                if (rotator) {
                    rotator.style.transform = `rotate(${screenAngle}deg)`;
                }
            }

            // Update Traversed Polyline (Glowing Trail)
            if (traversedPolyline) {
                const traversedCoords = fullFlightPath.slice(0, curIdx + 1);
                traversedCoords.push([curLat, curLng]);
                traversedPolyline.setLatLngs(traversedCoords);
            }

            // Update Tactical Dynamic Connector to User GPS Position & Distance
            if (userCurrentCoords) {
                if (userCargoConnector) {
                    userCargoConnector.setLatLngs([userCurrentCoords, [curLat, curLng]]);
                }
                const distCargo = haversineDistanceKm(userCurrentCoords[0], userCurrentCoords[1], curLat, curLng);
                const distTag = document.getElementById('userGpsDistanceTag');
                if (distTag) distTag.innerText = `Jarak ke Kargo: ${distCargo.toLocaleString()} km`;
            }

            // Camera Auto-Follow Feature
            if (autoFollow && flightProgress < 1.0) {
                if (cameraMode === 'cargo') {
                    liveMap.panTo([curLat, curLng], { animate: false });
                }
            }

            // Update GPS Readouts
            const latStr = `${Math.abs(curLat).toFixed(4)}° ${curLat >= 0 ? 'N' : 'S'}`;
            const lngStr = `${Math.abs(curLng).toFixed(4)}° ${curLng >= 0 ? 'E' : 'W'}`;
            const percentStr = (flightProgress * 100).toFixed(1) + '%';

            // Floating GPS Tag on map edge
            const gpsTag = document.getElementById('floatingGpsTag');
            if (gpsTag) gpsTag.innerText = `LAT: ${latStr} • LON: ${lngStr}`;

            // Tooltip Card Speed & Status
            const ttSpeed = document.getElementById('tooltipSpeed');
            if (ttSpeed) ttSpeed.innerText = 'Speed: 840 km/h';

            // Update Popup content if currently open
            const popCoords = document.getElementById('popCoords');
            if (popCoords) popCoords.innerText = `${latStr}, ${lngStr}`;

            const popProgress = document.getElementById('popProgress');
            if (popProgress) popProgress.innerText = percentStr;
        }

        animationFrameId = requestAnimationFrame(animateFlight);
    }

    // Controls: Play / Pause Toggle
    const playPauseBtn = document.getElementById('playPauseBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const playPauseText = document.getElementById('playPauseText');

    if (playPauseBtn) {
        playPauseBtn.addEventListener('click', function () {
            isPlaying = !isPlaying;
            if (isPlaying) {
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
                playPauseText.innerText = 'Pause';
                lastTimestamp = performance.now();
            } else {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
                playPauseText.innerText = 'Resume';
            }
        });
    }

    // Controls: Reset Flight
    document.getElementById('resetFlightBtn')?.addEventListener('click', function () {
        flightProgress = 0.0;
        lastTimestamp = performance.now();
        if (traversedPolyline) traversedPolyline.setLatLngs([]);
        if (fullFlightPath.length > 0 && planeMarker) {
            planeMarker.setLatLng(fullFlightPath[0]);
            liveMap.panTo(fullFlightPath[0], { animate: true });
        }
    });

    // Controls: Auto-Follow Toggle
    const autoFollowBtn = document.getElementById('autoFollowBtn');
    const followDot = document.getElementById('followDot');
    const followText = document.getElementById('followText');

    if (autoFollowBtn) {
        autoFollowBtn.addEventListener('click', function () {
            autoFollow = !autoFollow;
            if (autoFollow) {
                autoFollowBtn.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 text-[#2479E8] border border-blue-200 font-bold rounded-lg transition text-[11px]';
                followDot.className = 'w-2 h-2 rounded-full bg-[#2479E8] animate-pulse';
                followText.innerText = 'Auto-Follow: ON';
            } else {
                autoFollowBtn.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 text-slate-500 border border-slate-200 font-semibold rounded-lg transition text-[11px]';
                followDot.className = 'w-2 h-2 rounded-full bg-slate-400';
                followText.innerText = 'Auto-Follow: OFF';
            }
        });
    }

    // Controls: Speed Selector Buttons
    document.querySelectorAll('.speed-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.speed-btn').forEach(b => {
                b.classList.remove('bg-[#2479E8]', 'text-white');
                b.classList.add('bg-white', 'text-slate-600');
            });
            this.classList.remove('bg-white', 'text-slate-600');
            this.classList.add('bg-[#2479E8]', 'text-white');
            speedMultiplier = parseFloat(this.getAttribute('data-speed')) || 2;
        });
    });

    // Controls: Shipment Selector Dropdown
    const selector = document.getElementById('liveShipmentSelector');
    if (selector) {
        selector.addEventListener('change', function () {
            loadShipmentRoute(this.value);
        });
    }

    // Controls: Recent Shipments Row Click to Track
    document.querySelectorAll('.recent-shipment-row').forEach(row => {
        row.addEventListener('click', function () {
            const shipNum = this.getAttribute('data-shipment-num');
            if (selector) {
                // Find matching option in select
                for (let i = 0; i < selector.options.length; i++) {
                    if (selector.options[i].text.includes(shipNum)) {
                        selector.selectedIndex = i;
                        loadShipmentRoute(selector.value);
                        break;
                    }
                }
            }
            // Highlight active row
            document.querySelectorAll('.recent-shipment-row').forEach(r => r.classList.remove('bg-blue-50/70', 'ring-1', 'ring-blue-300'));
            this.classList.add('bg-blue-50/70', 'ring-1', 'ring-blue-300');
        });
    });

    // Initial load: fetch first shipment from selector
    if (selector && selector.value) {
        loadShipmentRoute(selector.value);
    } else {
        setupDefaultDemoRoute();
    }

    // =========================================================================
    // 2. ANALYTICS CONSOLE (Chart.js & Incidents)
    // =========================================================================
    const toggleBtn = document.getElementById('toggleTelematicsBtn');
    const content = document.getElementById('telematicsContent');
    const toggleText = document.getElementById('telematicsToggleText');
    const chevron = document.getElementById('telematicsChevron');
    let chartsInitialized = false;

    if (toggleBtn && content) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = content.classList.contains('hidden');
            if (isHidden) {
                content.classList.remove('hidden');
                toggleText.innerText = 'Hide Analytics';
                chevron.classList.add('rotate-180');
                if (!chartsInitialized) {
                    initCharts();
                    chartsInitialized = true;
                }
            } else {
                content.classList.add('hidden');
                toggleText.innerText = 'Show Analytics';
                chevron.classList.remove('rotate-180');
            }
        });
    }

    function initCharts() {
        // Status Doughnut Chart
        const statusEl = document.getElementById('statusChart');
        if (statusEl) {
            const statusCtx = statusEl.getContext('2d');
            const statusData = @json($metrics['status_chart']);
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: ['#94a3b8', '#3b82f6', '#6366f1', '#0284c7', '#10b981', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                    },
                    cutout: '70%',
                }
            });
        }

        // Carrier Bar Chart
        const carrierEl = document.getElementById('carrierChart');
        if (carrierEl) {
            const carrierCtx = carrierEl.getContext('2d');
            const carrierData = @json($metrics['carrier_chart']);
            new Chart(carrierCtx, {
                type: 'bar',
                data: {
                    labels: carrierData.map(c => c.name),
                    datasets: [{
                        label: 'Shipments',
                        data: carrierData.map(c => c.count),
                        backgroundColor: '#2563EB',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

});
</script>
@endpush
