<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F5F9] text-[#172033] antialiased selection:bg-[#F97316] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.public.title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        html { scroll-behavior: smooth; }
        .leaflet-container { font-family: inherit; z-index: 10; border-radius: 12px; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between bg-[#F4F5F9] text-[#172033] font-sans antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-[#E6EAF0] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#172033] flex items-center justify-center text-[#F97316]">
                    <i data-lucide="ship" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-lg font-extrabold tracking-tight text-[#172033] leading-none">
                        Cargo<span class="text-[#F97316]">Trace</span>
                    </div>
                    <span class="text-[9.5px] uppercase font-bold tracking-widest text-[#718096] block mt-0.5">
                        {{ __('ui.meta.global_visibility') }}
                    </span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-[#718096]">
                <a href="{{ route('home') }}" class="text-[#172033] hover:text-[#F97316] transition">{{ __('ui.public.nav_home') }}</a>
                <a href="#tracking" class="hover:text-[#F97316] transition">{{ __('ui.public.nav_tracking') }}</a>
                <a href="#services" class="hover:text-[#F97316] transition">{{ __('ui.public.nav_services') }}</a>
                <a href="#how-it-works" class="hover:text-[#F97316] transition">{{ __('ui.public.nav_how_it_works') }}</a>
                <a href="#network" class="hover:text-[#F97316] transition">{{ __('ui.public.nav_about') }}</a>
                <a href="#help" class="hover:text-[#F97316] transition">{{ __('ui.public.nav_help') }}</a>
            </nav>

            <div class="flex items-center gap-2.5">
                <x-language-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl transition">
                        <span>{{ __('ui.public.ops_dashboard') }}</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block px-3.5 py-2 text-xs font-bold text-[#172033] hover:text-[#F97316] transition">
                        {{ __('ui.public.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white text-xs font-bold rounded-xl transition">
                        {{ __('ui.public.register') }}
                    </a>
                @endauth
                <button type="button" id="publicNavToggle" class="md:hidden p-2 rounded-xl border border-[#E6EAF0] text-[#172033]" aria-label="{{ __('ui.public.open_nav_aria') }}">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div id="publicNavMobile" class="hidden md:hidden border-t border-[#E6EAF0] bg-white px-4 py-4 space-y-2 text-sm font-semibold">
            <a href="#tracking" class="block px-3 py-2 rounded-lg hover:bg-[#F4F5F9]">{{ __('ui.public.nav_tracking') }}</a>
            <a href="#services" class="block px-3 py-2 rounded-lg hover:bg-[#F4F5F9]">{{ __('ui.public.nav_services') }}</a>
            <a href="#how-it-works" class="block px-3 py-2 rounded-lg hover:bg-[#F4F5F9]">{{ __('ui.public.nav_how_it_works') }}</a>
            <a href="#help" class="block px-3 py-2 rounded-lg hover:bg-[#F4F5F9]">{{ __('ui.public.nav_help') }}</a>
            <div class="pt-2 border-t border-[#E6EAF0] flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex-1 text-center py-2 bg-[#F97316] text-white rounded-xl text-xs font-bold">{{ __('ui.public.open_ops_dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 bg-[#F4F5F9] text-[#172033] rounded-xl text-xs font-bold">{{ __('ui.public.login') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-[#172033] text-white rounded-xl text-xs font-bold">{{ __('ui.public.register') }}</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="tracking" class="relative bg-[#172033] overflow-hidden border-b border-[#E6EAF0]">
        <img src="{{ asset('images/dashboard-hero-roro.jpg') }}"
             onerror="this.src='{{ asset('images/roro-promo-card.png') }}'"
             alt="CargoTrace Global Vehicle Carrier"
             class="absolute inset-0 w-full h-full object-cover opacity-35">
        <div class="absolute inset-0 bg-gradient-to-r from-[#172033] via-[#172033]/92 to-[#172033]/55"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <div class="max-w-3xl space-y-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-bold tracking-wider text-[#FDBA74] uppercase">
                    <span class="w-2 h-2 rounded-full bg-[#F97316]"></span>
                    <span>{{ __('ui.public.hero_badge') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.12]">
                    {{ __('ui.public.hero_title_1') }} <span class="text-[#F97316]">{{ __('ui.public.hero_title_highlight') }}</span>, {{ __('ui.public.hero_title_2') }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed">
                    {{ __('ui.public.hero_desc') }}
                </p>

                <!-- Search Box -->
                <form method="GET" action="{{ route('tracking.public') }}" class="pt-2">
                    <div class="bg-white p-2 rounded-2xl shadow-lg flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="flex items-center flex-1 px-3 py-2">
                            <i data-lucide="search" class="w-5 h-5 text-[#F97316] shrink-0 mr-3"></i>
                            <input type="text" name="tracking_number" value="{{ $trackingNumber }}" required
                                   placeholder="{{ __('ui.public.tracking_placeholder') }}"
                                   class="w-full bg-transparent text-sm font-semibold text-[#172033] placeholder-[#718096] focus:outline-none uppercase tracking-wide">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-[#F97316] hover:bg-[#EA580C] text-white text-sm font-bold rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shrink-0">
                            <span>{{ __('ui.public.track_button') }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>

                <!-- Sample Tracking Numbers -->
                <div class="flex flex-wrap items-center gap-2 text-xs pt-1">
                    <span class="text-slate-400 font-medium">{{ __('ui.public.sample_label') }}</span>
                    @forelse($sampleShipments as $samp)
                        <a href="{{ route('tracking.public', ['tracking_number' => $samp->shipment_number]) }}"
                           class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-[#F97316] border border-white/15 text-xs font-mono font-semibold text-white transition">
                            {{ $samp->shipment_number }}
                        </a>
                    @empty
                        <span class="text-xs font-mono text-slate-300">CT-2026-RORO-8841</span>
                    @endforelse
                </div>

                <!-- Evaluator Quick Role Demo Bar -->
                <div class="pt-4 border-t border-white/10 flex flex-wrap items-center gap-2.5 text-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('ui.public.demo_ribbon_title') }}:</span>
                    <a href="{{ route('demo.login', 'admin') }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold border border-white/15 transition">Admin</a>
                    <a href="{{ route('demo.login', 'operator') }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold border border-white/15 transition">Operator</a>
                    <a href="{{ route('demo.login', 'customer') }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-[#FDBA74] font-semibold border border-white/15 transition">{{ __('ui.roles.customer') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Active Tracking Result Card -->
    @if($shipment)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-14 relative z-20 w-full">
            <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-[0_12px_32px_rgba(23,32,51,0.08)] p-6 sm:p-8 space-y-6">

                <!-- Header Summary -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#E6EAF0] pb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-[#172033] text-white">{{ __('ui.public.active_consignment') }}</span>
                            <h2 class="text-2xl font-extrabold text-[#172033] font-mono">{{ $shipment->shipment_number }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-50 text-[#3B82F6] border border-blue-200">{{ __('ui.status.' . $shipment->status) }}</span>
                            @if($shipment->is_delayed)
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-50 text-[#F59E0B] border border-amber-200">
                                    {{ __('ui.public.delayed_badge', ['hours' => $shipment->delay_hours, 'severity' => __('ui.severity.' . ($shipment->delay_severity ?? 'NONE'))]) }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-[#10A37F] border border-emerald-200">
                                    {{ __('ui.public.on_schedule_badge') }}
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-[#718096] mt-2 flex flex-wrap items-center gap-2">
                            <span>{{ __('ui.public.carrier_label') }} <strong class="text-[#172033]">{{ $shipment->carrier->name }}</strong></span>
                            <span>&bull;</span>
                            <span>{{ __('ui.public.route_label') }} <strong class="text-[#172033]">{{ $shipment->origin->city }}</strong> &rarr; <strong class="text-[#172033]">{{ $shipment->destination->city }}</strong></span>
                            <span>&bull;</span>
                            <span>{{ __('ui.shipments.current_location_label') }} <strong class="text-[#F97316]">{{ $shipment->latestTrackingEvent?->location_name ?? $shipment->origin->name }}</strong></span>
                        </div>
                    </div>

                    <div class="bg-[#F4F5F9] p-3.5 rounded-xl border border-[#E6EAF0] text-xs lg:text-right shrink-0">
                        <span class="text-[10px] uppercase font-bold text-[#718096] block">{{ __('ui.public.eta_label') }}</span>
                        <span class="text-[#172033] font-extrabold text-sm block mt-0.5">{{ $shipment->expected_arrival->format('M d, Y H:i') }}</span>
                        @if($shipment->is_delayed)
                            <span class="text-[#F97316] font-bold block mt-0.5">{{ __('ui.public.adjusted_eta') }} {{ $shipment->estimated_arrival?->format('M d, Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Map & Weather -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="flex items-center justify-between text-xs font-bold text-[#172033] mb-2">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="map" class="w-4 h-4 text-[#F97316]"></i>
                                <span>{{ __('ui.public.gis_route_title') }}</span>
                            </span>
                            <span class="text-[11px] font-mono text-[#F97316]">{{ __('ui.public.waypoints_monitored', ['count' => count($mapPoints)]) }}</span>
                        </div>
                        <div id="publicMapTileError" class="hidden mb-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 font-medium">
                            {{ __('ui.shipments.map_tile_error') }}
                        </div>
                        <div id="publicMap" class="w-full h-80 sm:h-96 rounded-xl border border-[#E6EAF0]"></div>
                    </div>

                    <div class="bg-[#F4F5F9] p-5 rounded-xl border border-[#E6EAF0] flex flex-col justify-between text-xs">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-[#E6EAF0]">
                                <div>
                                    <h4 class="font-bold text-[#172033] text-sm">{{ __('ui.public.weather_title') }}</h4>
                                    <span class="text-[10px] text-[#718096]">Open-Meteo REST API</span>
                                </div>
                                <span class="text-2xl">{{ $weather['icon'] ?? '🌤️' }}</span>
                            </div>

                            @if($weather)
                                <div class="mt-4 space-y-3">
                                    <div class="bg-white p-3 rounded-xl border border-[#E6EAF0]">
                                        <span class="text-[10px] font-bold text-[#718096] uppercase">{{ __('ui.public.weather_station') }}</span>
                                        <div class="font-bold text-[#172033] text-sm mt-0.5">{{ $weather['location'] }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-center">
                                        <div class="bg-white p-3 rounded-xl border border-[#E6EAF0]">
                                            <span class="text-[10px] text-[#718096] uppercase font-bold block">{{ __('ui.public.air_temp') }}</span>
                                            <span class="text-base font-extrabold text-[#F97316] mt-1 block">{{ $weather['temperature'] }}{{ $weather['temperature_unit'] }}</span>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-[#E6EAF0]">
                                            <span class="text-[10px] text-[#718096] uppercase font-bold block">{{ __('ui.public.wind_speed') }}</span>
                                            <span class="text-base font-extrabold text-[#172033] mt-1 block">{{ $weather['wind_speed'] }} <span class="text-xs font-normal text-[#718096]">{{ $weather['wind_speed_unit'] }}</span></span>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-[#E6EAF0]">
                                        {{ __('ui.public.atmospheric_condition') }} <strong class="text-[#172033]">{{ $weather['condition'] }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-[10px] text-[#718096] pt-3 border-t border-[#E6EAF0] text-center space-y-0.5">
                            <div>{{ __('ui.public.weather_sync_note') }}</div>
                            <div class="italic">{{ __('ui.shipments.weather_disclaimer') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Manifest -->
                @if($shipment->items->isNotEmpty())
                    <div class="pt-4 border-t border-[#E6EAF0]">
                        <h3 class="text-sm font-extrabold text-[#172033] mb-3 flex items-center gap-2">
                            <i data-lucide="car" class="w-4 h-4 text-[#F97316]"></i>
                            <span>{{ __('ui.public.vehicle_manifest', ['count' => $shipment->items->sum('quantity')]) }}</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                            @foreach($shipment->items as $item)
                                <div class="bg-[#F4F5F9] p-4 rounded-xl border border-[#E6EAF0] space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-[#172033]">{{ $item->vehicle_model ?? $item->item_description }}</span>
                                        <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full bg-[#FFF7ED] text-[#F97316] border border-[#FDBA74]/60">{{ $item->cargo_category }}</span>
                                    </div>
                                    @if($item->vin_number)
                                        <div class="text-[11px] font-mono text-[#718096]">VIN: <strong class="text-[#172033]">{{ $item->vin_number }}</strong></div>
                                    @endif
                                    <div class="text-[11px] text-[#718096] flex items-center justify-between pt-1">
                                        <span>{{ __('ui.common.qty') }}: <strong class="text-[#172033]">{{ $item->quantity }}</strong></span>
                                        <span>{{ number_format($item->weight_kg) }} kg</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Chronological Timeline -->
                <div class="pt-4 border-t border-[#E6EAF0]">
                    <h3 class="text-sm font-extrabold text-[#172033] mb-4 flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-[#F97316]"></i>
                        <span>{{ __('ui.public.milestones_title') }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        @foreach($shipment->trackingEvents as $evt)
                            <div class="bg-[#F4F5F9] p-4 rounded-xl border {{ $evt->is_exception ? 'border-[#F04452]/50 bg-red-50/40' : 'border-[#E6EAF0]' }} flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between font-bold text-[#172033] mb-1.5 gap-2">
                                        <span class="truncate">{{ $evt->location_name }}</span>
                                        <span class="text-[9px] uppercase px-2 py-0.5 rounded-full {{ $evt->is_exception ? 'bg-[#F04452] text-white' : 'bg-white text-[#F97316] border border-[#FDBA74]/60' }} font-mono shrink-0">
                                            {{ __('ui.status.' . $evt->status) }}
                                        </span>
                                    </div>
                                    <p class="text-[#718096] text-[11px] leading-relaxed">{{ $evt->description }}</p>
                                </div>
                                <div class="text-[10px] text-[#718096] font-mono mt-3 pt-2.5 border-t border-[#E6EAF0]">
                                    {{ $evt->event_time->format('d M Y, H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @elseif(!empty($trackingNumber))
        <section class="max-w-xl mx-auto px-4 py-10">
            <div class="bg-white border border-[#E6EAF0] rounded-2xl p-6 text-center space-y-2 shadow-saas">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-[#F04452] flex items-center justify-center mx-auto">
                    <i data-lucide="search-x" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.public.not_found_title') }}</h3>
                <p class="text-xs text-[#718096]">{{ __('ui.public.not_found_desc', ['number' => $trackingNumber]) }}</p>
            </div>
        </section>
    @endif

    <!-- Services Section -->
    <section id="services" class="py-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#F97316]">{{ __('ui.public.services_kicker') }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#172033] mt-1">{{ __('ui.public.services_heading') }}</h2>
            <p class="text-xs sm:text-sm text-[#718096] mt-2">{{ __('ui.public.services_desc') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['icon' => 'radar', 'title' => __('ui.public.service_1_title'), 'desc' => __('ui.public.service_1_desc'), 'foot' => __('ui.public.service_1_foot')],
                ['icon' => 'shield-check', 'title' => __('ui.public.service_2_title'), 'desc' => __('ui.public.service_2_desc'), 'foot' => __('ui.public.service_2_foot')],
                ['icon' => 'globe', 'title' => __('ui.public.service_3_title'), 'desc' => __('ui.public.service_3_desc'), 'foot' => __('ui.public.service_3_foot')],
                ['icon' => 'headphones', 'title' => __('ui.public.service_4_title'), 'desc' => __('ui.public.service_4_desc'), 'foot' => __('ui.public.service_4_foot')],
            ] as $svc)
                <div class="bg-white p-6 rounded-2xl border border-[#E6EAF0] hover:border-[#F97316]/50 transition flex flex-col justify-between">
                    <div>
                        <div class="w-11 h-11 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center mb-4">
                            <i data-lucide="{{ $svc['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-[#172033]">{{ $svc['title'] }}</h3>
                        <p class="text-xs text-[#718096] mt-2 leading-relaxed">{{ $svc['desc'] }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E6EAF0] text-[11px] font-bold text-[#172033]">{{ $svc['foot'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- How It Works + Global Network -->
    <section id="how-it-works" class="py-14 bg-white border-y border-[#E6EAF0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-6 space-y-5">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-[#F97316]">{{ __('ui.public.how_kicker') }}</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#172033] mt-1">{{ __('ui.public.how_heading') }}</h2>
                    <p class="text-xs sm:text-sm text-[#718096] mt-1.5">{{ __('ui.public.how_desc') }}</p>
                </div>

                <div class="space-y-3">
                    @foreach([
                        ['num' => '01', 'title' => __('ui.public.step_1_title'), 'desc' => __('ui.public.step_1_desc')],
                        ['num' => '02', 'title' => __('ui.public.step_2_title'), 'desc' => __('ui.public.step_2_desc')],
                        ['num' => '03', 'title' => __('ui.public.step_3_title'), 'desc' => __('ui.public.step_3_desc')],
                    ] as $step)
                        <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex items-start gap-3.5">
                            <span class="w-8 h-8 rounded-lg bg-[#172033] text-[#FDBA74] font-mono font-bold text-xs flex items-center justify-center shrink-0">{{ $step['num'] }}</span>
                            <div>
                                <div class="text-sm font-bold text-[#172033]">{{ $step['title'] }}</div>
                                <p class="text-xs text-[#718096] mt-0.5">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="network" class="lg:col-span-6 space-y-4">
                <div class="rounded-2xl overflow-hidden border border-[#E6EAF0]">
                    <img src="{{ asset('images/port-hub-crane.jpg') }}"
                         onerror="this.src='{{ asset('images/roro-promo-card.png') }}'"
                         alt="CargoTrace Global Port Network"
                         class="w-full h-64 object-cover">
                </div>
                <div class="p-5 rounded-2xl bg-[#F4F5F9] border border-[#E6EAF0] space-y-2.5 text-xs">
                    <div class="font-extrabold text-[#172033] text-sm">{{ __('ui.public.pillars_title') }}</div>
                    <div class="text-[#718096]"><strong class="text-[#172033]">{{ __('ui.public.pillar_1_title') }}</strong> {{ __('ui.public.pillar_1_desc') }}</div>
                    <div class="text-[#718096]"><strong class="text-[#172033]">{{ __('ui.public.pillar_2_title') }}</strong> {{ __('ui.public.pillar_2_desc') }}</div>
                    <div class="text-[#718096]"><strong class="text-[#172033]">{{ __('ui.public.pillar_3_title') }}</strong> {{ __('ui.public.pillar_3_desc') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Support Banner -->
    <section id="help" class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="rounded-2xl bg-[#172033] p-6 sm:p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-widest text-[#FDBA74]">{{ __('ui.public.help_kicker') }}</span>
                <h3 class="text-xl sm:text-2xl font-extrabold mt-1">{{ __('ui.public.help_heading') }}</h3>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl mt-1">{{ __('ui.public.help_desc') }}</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('login') }}" class="px-5 py-3 rounded-xl bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold transition">
                    {{ __('ui.public.contact_support') }}
                </a>
                <a href="#tracking" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">
                    {{ __('ui.public.check_again') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#E6EAF0] py-8 text-xs text-[#718096]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div><strong class="text-[#172033]">Cargo<span class="text-[#F97316]">Trace</span></strong> &copy; {{ date('Y') }}. {{ __('ui.public.footer_rights') }}</div>
            <div class="text-[11px]">Ro-Ro &amp; Air Freight Standards &bull; MySQL &bull; Laravel &bull; OpenStreetMap</div>
        </div>
    </footer>

    @if($shipment && !empty($mapPoints))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

        const points = @json($mapPoints);
        if (points.length === 0) return;

        const map = L.map('publicMap').setView([points[0].lat, points[0].lng], 4);
        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
        }).addTo(map);

        const tileErr = document.getElementById('publicMapTileError');
        tiles.on('tileerror', () => tileErr?.classList.remove('hidden'));
        tiles.on('load', () => tileErr?.classList.add('hidden'));

        const latLngs = [];
        const makeIcon = (color, symbol) => L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color:${color};width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:11px;border:2px solid white;box-shadow:0 2px 6px rgba(23,32,51,0.3);">${symbol}</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        });

        points.forEach((pt) => {
            latLngs.push([pt.lat, pt.lng]);
            let icon = makeIcon('#3B82F6', '•');
            if (pt.type === 'origin') icon = makeIcon('#10A37F', 'O');
            if (pt.type === 'destination') icon = makeIcon('#F97316', 'D');
            if (pt.type === 'exception') icon = makeIcon('#F04452', '!');

            L.marker([pt.lat, pt.lng], { icon }).addTo(map)
                .bindPopup(`<strong>${escapeHtml(pt.name)}</strong><br/>${escapeHtml(pt.city)}<br/><span>Status: ${escapeHtml(pt.status || 'WAYPOINT')}</span><br/><span style="color:#F97316;font-weight:bold;">${escapeHtml(pt.time)}</span>${pt.desc ? `<p style="margin-top:4px;font-size:11px;color:#718096;">${escapeHtml(pt.desc)}</p>` : ''}`);
        });

        if (latLngs.length > 1) {
            const polyline = L.polyline(latLngs, { color: '#F97316', weight: 3.5, dashArray: '6, 8' }).addTo(map);
            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        }
    });
    </script>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) lucide.createIcons();
        const toggleBtn = document.getElementById('publicNavToggle');
        const mobileMenu = document.getElementById('publicNavMobile');
        if (toggleBtn && mobileMenu) {
            toggleBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        }
    });
    </script>
</body>
</html>
