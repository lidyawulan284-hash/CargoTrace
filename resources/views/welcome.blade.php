<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace - Global Shipment Visibility & Tracking Platform</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="min-h-full flex flex-col justify-between">

    <!-- Top Navigation Bar -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-blue-600 flex items-center justify-center shadow-lg shadow-sky-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-black tracking-tight text-white">Cargo<span class="text-sky-400">Trace</span></span>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 block -mt-1 font-bold">Global Visibility Platform</span>
                </div>
            </a>

            <div class="flex items-center space-x-3 text-xs font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow-lg transition">
                        Open Operations Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-slate-300 hover:text-white transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow-lg transition">
                        Customer Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Tracking Search Section -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full space-y-10">
        
        <!-- Hero Title & Search Bar -->
        <div class="text-center max-w-3xl mx-auto space-y-4 pt-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-950 text-sky-400 border border-sky-800">
                <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                UAS Final Project Portfolio Platform
            </span>

            <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                Real-Time End-to-End <br/>
                <span class="bg-gradient-to-r from-sky-400 via-blue-400 to-indigo-300 bg-clip-text text-transparent">Shipment Visibility & Telematics</span>
            </h1>

            <p class="text-sm text-slate-400 max-w-xl mx-auto">
                Comprehensive tracking engine implementing strict lifecycle state machines, deterministic delay rule evaluation, live GIS mapping, and Open-Meteo weather intelligence.
            </p>

            <!-- Search Input Box -->
            <form method="GET" action="{{ route('home') }}" class="max-w-2xl mx-auto mt-6">
                <div class="relative flex items-center bg-slate-800/90 border border-slate-700 rounded-2xl p-2 shadow-2xl focus-within:ring-2 focus-within:ring-sky-500 transition">
                    <div class="pl-3 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="tracking_number" value="{{ $trackingNumber }}" required
                           placeholder="Enter Tracking Reference (e.g. CT-2026-SEA-8841)..." 
                           class="w-full bg-transparent px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none font-mono uppercase">
                    <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-lg transition">
                        Track Consignment
                    </button>
                </div>
            </form>

            <!-- Sample Tracking Numbers & Quick Demo Logins -->
            <div class="pt-2 text-xs text-slate-400">
                <span>Try sample tracking codes: </span>
                @foreach($sampleShipments as $samp)
                    <a href="{{ route('home', ['tracking_number' => $samp->shipment_number]) }}" class="font-mono text-sky-400 hover:underline mr-2">
                        {{ $samp->shipment_number }}
                    </a>
                @endforeach
            </div>

            <!-- Instant Grading Login Ribbon (Slide 19) -->
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 max-w-xl mx-auto mt-4 text-xs">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    ⚡ Instant Evaluator 1-Click Access (Bypass Login)
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <a href="{{ route('demo.login', 'admin') }}" class="px-3 py-1.5 rounded-lg bg-purple-950/70 text-purple-300 border border-purple-800 hover:bg-purple-900 transition font-bold">
                        👑 Admin Demo
                    </a>
                    <a href="{{ route('demo.login', 'operator') }}" class="px-3 py-1.5 rounded-lg bg-blue-950/70 text-blue-300 border border-blue-800 hover:bg-blue-900 transition font-bold">
                        ⚙️ Operator Demo
                    </a>
                    <a href="{{ route('demo.login', 'customer') }}" class="px-3 py-1.5 rounded-lg bg-emerald-950/70 text-emerald-300 border border-emerald-800 hover:bg-emerald-900 transition font-bold">
                        📦 Customer Demo
                    </a>
                </div>
            </div>

        </div>

        <!-- Tracked Shipment Result Card (If Searched) -->
        @if($shipment)
            <div class="bg-slate-800/90 rounded-3xl border border-slate-700 p-6 shadow-2xl space-y-6">
                
                <!-- Shipment Header Ribbon -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-700/60 pb-5">
                    <div>
                        <div class="flex items-center space-x-3">
                            <h2 class="text-2xl font-black text-white font-mono">{{ $shipment->shipment_number }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-sky-950 text-sky-300 border border-sky-800">
                                {{ $shipment->status }}
                            </span>
                            @if($shipment->is_delayed)
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-950 text-amber-300 border border-amber-800">
                                    ⚠️ DELAYED +{{ $shipment->delay_hours }}H ({{ $shipment->delay_severity }})
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-950 text-emerald-300 border border-emerald-800">
                                    ✓ ON SCHEDULE
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-1">
                            Carrier: <strong class="text-slate-200">{{ $shipment->carrier->name }}</strong> &bull; 
                            Transit: <strong class="text-slate-200">{{ $shipment->origin->city }}</strong> &rarr; <strong class="text-slate-200">{{ $shipment->destination->city }}</strong>
                        </div>
                    </div>

                    <div class="text-right text-xs">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Baseline vs Forecast ETA</span>
                        <span class="text-white font-bold block">{{ $shipment->expected_arrival->format('M d, Y') }}</span>
                        @if($shipment->is_delayed)
                            <span class="text-amber-400 text-[11px] font-semibold">Updated: {{ $shipment->estimated_arrival?->format('M d, Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <!-- GIS Map & Weather Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="text-xs font-bold text-slate-300 mb-2 flex items-center justify-between">
                            <span>Interactive Transit Path (Leaflet.js + OSM)</span>
                            <span class="text-[10px] text-slate-400">{{ count($mapPoints) }} checkpoints mapped</span>
                        </div>
                        <div id="publicMap" class="w-full h-80 rounded-2xl border border-slate-700 z-10"></div>
                    </div>

                    <!-- Weather Card -->
                    <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-700/80 flex flex-col justify-between text-xs">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div>
                                    <h4 class="font-bold text-white">Atmospheric Fix</h4>
                                    <span class="text-[10px] text-slate-400">Open-Meteo REST service</span>
                                </div>
                                <span class="text-2xl">{{ $weather['icon'] ?? '🌤️' }}</span>
                            </div>

                            @if($weather)
                                <div class="mt-4 space-y-3">
                                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Station Location</span>
                                        <div class="font-bold text-white text-sm mt-0.5">{{ $weather['location'] }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-center">
                                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700">
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Temperature</span>
                                            <span class="text-base font-black text-sky-400">{{ $weather['temperature'] }}{{ $weather['temperature_unit'] }}</span>
                                        </div>
                                        <div class="bg-slate-800/60 p-2.5 rounded-xl border border-slate-700">
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Wind Speed</span>
                                            <span class="text-base font-black text-sky-400">{{ $weather['wind_speed'] }} <span class="text-xs font-normal">{{ $weather['wind_speed_unit'] }}</span></span>
                                        </div>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-800/80 text-slate-300 text-[11px]">
                                        Condition: <strong>{{ $weather['condition'] }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-[10px] text-slate-500 pt-3 border-t border-slate-800 text-center">
                            Synchronized live with coordinate telemetry
                        </div>
                    </div>
                </div>

                <!-- Chronological Checkpoint Journey -->
                <div class="pt-4 border-t border-slate-700/60">
                    <h3 class="text-sm font-bold text-white mb-4">Voyage Checkpoint Milestones</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        @foreach($shipment->trackingEvents as $evt)
                            <div class="bg-slate-900/80 p-3.5 rounded-xl border border-slate-700/80 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between font-bold text-slate-200">
                                        <span class="truncate">{{ $evt->location_name }}</span>
                                        <span class="text-[9px] uppercase px-1.5 py-0.2 rounded bg-slate-800 text-sky-400 font-mono">{{ $evt->status }}</span>
                                    </div>
                                    <p class="text-slate-400 mt-1 line-clamp-2">{{ $evt->description }}</p>
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-2 pt-2 border-t border-slate-800">
                                    {{ $evt->event_time->format('M d, H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @elseif(!empty($trackingNumber))
            <div class="bg-rose-950/40 border border-rose-800 rounded-2xl p-6 text-center text-xs text-rose-300 max-w-md mx-auto">
                <span class="text-2xl block mb-2">🔍</span>
                Shipment reference <strong>{{ $trackingNumber }}</strong> was not found in the CargoTrace platform. Please verify the consignment code.
            </div>
        @endif

        <!-- Tech Stack & Engineering Highlights (Slide 14 & 18) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
            <div class="bg-slate-800/40 border border-slate-800 p-5 rounded-2xl space-y-2">
                <div class="text-sky-400 text-lg">⚙️</div>
                <h4 class="font-bold text-white text-sm">Deterministic ETA & Lifecycle</h4>
                <p class="text-xs text-slate-400">Strict transition state machines prevent illegal status regressions. Delay severity categorizes deviations into Low, Medium, and High.</p>
            </div>
            <div class="bg-slate-800/40 border border-slate-800 p-5 rounded-2xl space-y-2">
                <div class="text-sky-400 text-lg">🗺️</div>
                <h4 class="font-bold text-white text-sm">Spatial GIS & External Weather</h4>
                <p class="text-xs text-slate-400">Leaflet.js interactive OpenStreetMap polylines with automated fallback integration to Open-Meteo REST API.</p>
            </div>
            <div class="bg-slate-800/40 border border-slate-800 p-5 rounded-2xl space-y-2">
                <div class="text-sky-400 text-lg">🔒</div>
                <h4 class="font-bold text-white text-sm">Sanctum REST API & RBAC</h4>
                <p class="text-xs text-slate-400">Complete API endpoints for shipments, checkpoints, and carriers with role-based policies and audit trail tracking.</p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        CargoTrace Global Visibility Platform &bull; Web Programming 2 UAS Final Project &bull; {{ date('Y') }}
    </footer>

    @if($shipment && !empty($mapPoints))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const points = @json($mapPoints);
        if (points.length === 0) return;

        const map = L.map('publicMap').setView([points[0].lat, points[0].lng], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const latLngs = [];
        points.forEach((pt) => {
            latLngs.push([pt.lat, pt.lng]);
            const marker = L.marker([pt.lat, pt.lng]).addTo(map);
            marker.bindPopup(`<strong>${pt.name}</strong><br/>${pt.city}<br/><span style="color:#0284c7;">${pt.time}</span>`);
        });

        if (latLngs.length > 1) {
            const polyline = L.polyline(latLngs, { color: '#38bdf8', weight: 3, dashArray: '5, 8' }).addTo(map);
            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        }
    });
    </script>
    @endif

</body>
</html>
