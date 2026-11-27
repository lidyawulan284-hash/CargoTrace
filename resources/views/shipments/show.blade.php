@extends('layouts.app')

@section('title', __('ui.shipments.show_title', ['number' => $shipment->shipment_number]))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <a href="{{ route('shipments.index') }}" class="hover:text-[#F97316] transition font-medium">{{ __('ui.nav.vehicle_shipments') }}</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033] font-mono">{{ $shipment->shipment_number }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Navigation & Status Ribbon -->
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight font-mono">
                        {{ $shipment->shipment_number }}
                    </h1>

                    @php
                        $badge = match($shipment->status) {
                            'CREATED' => 'bg-slate-100 text-[#718096] border-[#E6EAF0]',
                            'BOOKED' => 'bg-amber-50 text-[#F59E0B] border-amber-200',
                            'PICKED_UP' => 'bg-[#FFF7ED] text-[#F97316] border-[#FDBA74]/60',
                            'IN_TRANSIT' => 'bg-blue-50 text-[#3B82F6] border-blue-200 font-bold',
                            'DELIVERED' => 'bg-emerald-50 text-[#10A37F] border-emerald-200 font-bold',
                            default => 'bg-red-50 text-[#F04452] border-red-200',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $badge }}">
                        {{ __('ui.status.' . $shipment->status) }}
                    </span>

                    @if($shipment->is_delayed)
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-50 text-[#F59E0B] border border-amber-200 flex items-center gap-1.5">
                            <span>{{ __('ui.shipments.delayed_badge', ['hours' => $shipment->delay_hours]) }}</span>
                            <span class="text-[9px] bg-[#F59E0B] text-white px-1.5 py-0.5 rounded">{{ __('ui.severity.' . ($shipment->delay_severity ?? 'NONE')) }}</span>
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-[#10A37F] border border-emerald-200">
                            {{ __('ui.shipments.on_schedule_badge') }}
                        </span>
                    @endif
                </div>

                <div class="text-xs text-[#718096] mt-2 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span>{{ __('ui.shipments.shipper') }} <strong class="text-[#172033]">{{ $shipment->customer->company_name ?? $shipment->customer->name }}</strong></span>
                    <span>&bull;</span>
                    <span>{{ __('ui.shipments.carrier_label') }} <strong class="text-[#172033]">{{ $shipment->carrier->name }}</strong> ({{ $shipment->carrier->code }} &bull; {{ $shipment->carrier->service_type }})</span>
                    <span>&bull;</span>
                    <span>{{ __('ui.shipments.route_label') }} <strong class="text-[#172033]">{{ $shipment->origin->city }} ({{ $shipment->origin->code }})</strong> &rarr; <strong class="text-[#172033]">{{ $shipment->destination->city }} ({{ $shipment->destination->code }})</strong></span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center gap-1">{{ __('ui.shipments.current_location_label') }} <strong class="text-[#F97316]">{{ $summary['current_location'] ?? $shipment->origin->name }}</strong></span>
                </div>
            </div>

            <!-- Operator Action Buttons -->
            @if(Auth::user()->canManageShipments())
                <div class="flex flex-wrap items-center gap-2">
                    @if(!$shipment->isTerminal())
                        <button type="button" onclick="document.getElementById('checkpointModal').classList.remove('hidden')"
                                class="px-3.5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>{{ __('ui.shipments.add_checkpoint') }}</span>
                        </button>
                        <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                                class="px-3.5 py-2 bg-[#F04452] hover:bg-red-600 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>{{ __('ui.shipments.flag_incident') }}</span>
                        </button>
                    @endif
                    <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3.5 py-2 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        <span>{{ __('ui.common.edit') }}</span>
                    </a>
                </div>
            @endif

        </div>

        <!-- Schedule & Deterministic Delay Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-[#E6EAF0] text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('ui.shipments.scheduled_departure') }}</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $shipment->expected_departure->format('M d, Y H:i') }}</span>
                <span class="text-[10px] text-slate-400">{{ __('ui.shipments.actual_label') }} {{ $shipment->actual_departure ? $shipment->actual_departure->format('M d, H:i') : __('ui.common.pending') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('ui.shipments.baseline_eta') }}</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $shipment->expected_arrival->format('M d, Y H:i') }}</span>
                <span class="text-[10px] text-slate-400">{{ __('ui.shipments.sla_threshold_label', ['hours' => $summary['sla_threshold_hours'] ?? 12]) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('ui.shipments.dynamic_eta') }}</span>
                <span class="font-bold {{ $shipment->is_delayed ? 'text-amber-600' : 'text-emerald-600' }} text-sm mt-0.5 block">
                    {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('M d, Y H:i') : $shipment->expected_arrival->format('M d, Y H:i') }}
                </span>
                <span class="text-[10px] text-slate-400">{{ __('ui.shipments.deterministic_eta') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ __('ui.shipments.delay_discrepancy') }}</span>
                @if($shipment->is_delayed)
                    <div class="mt-0.5">
                        <span class="font-black text-amber-700 text-sm">{{ __('ui.shipments.hours_late_full', ['hours' => $shipment->delay_hours]) }}</span>
                        <span class="text-[10px] block text-amber-600 font-bold">
                            {{ __('ui.shipments.severity_label') }} {{ __('ui.severity.' . ($shipment->delay_severity ?? 'NONE')) }}
                            @if($summary['exceeds_sla_threshold'] ?? false)
                                &bull; {{ __('ui.shipments.exceeds_sla', ['hours' => $summary['sla_threshold_hours']]) }}
                            @endif
                        </span>
                    </div>
                @else
                    <span class="font-bold text-emerald-600 text-sm mt-0.5 block">{{ __('ui.shipments.nominal_hours') }}</span>
                    <span class="text-[10px] text-emerald-600">{{ __('ui.shipments.within_sla', ['hours' => $summary['sla_threshold_hours'] ?? 12]) }}</span>
                @endif
            </div>
        </div>

        @if(!empty($summary['delay_reason']))
            <div class="mt-4 px-4 py-2.5 rounded-xl border text-xs flex flex-wrap items-center justify-between gap-2 {{ $shipment->is_delayed ? 'bg-amber-50/70 border-amber-200 text-amber-900' : 'bg-emerald-50/60 border-emerald-200 text-emerald-900' }}">
                <div>
                    <strong class="font-bold">{{ __('ui.shipments.delay_reason_label') }}</strong>
                    <span>{{ $summary['delay_reason'] }}</span>
                </div>
                <span class="font-mono text-[10px] opacity-80">
                    Expected: {{ $shipment->expected_arrival->format('Y-m-d H:i') }} | Estimated: {{ ($shipment->estimated_arrival ?? $shipment->expected_arrival)->format('Y-m-d H:i') }} | SLA: {{ $summary['sla_threshold_hours'] ?? 12 }}h
                </span>
            </div>
        @endif
    </div>

    <!-- Map & Weather Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E6EAF0] shadow-sm p-4 flex flex-col">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#F97316] animate-ping"></span>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('ui.shipments.route_map_title') }}</h3>
                </div>
                <span class="text-[10px] text-slate-400">Leaflet.js &bull; OpenStreetMap Tiles</span>
            </div>

            <div id="shipmentMapTileError" class="hidden mb-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 font-medium">
                {{ __('ui.shipments.map_tile_error') }}
            </div>

            <div id="shipmentMap" class="w-full h-80 rounded-xl border border-slate-200 z-10 flex-1"></div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 px-2">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> {{ __('ui.dashboard.origin') }}</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#F97316] inline-block"></span> {{ __('ui.shipments.checkpoint_fix') }}</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> {{ __('ui.dashboard.destination') }}</span>
                </div>
                <span>{{ __('ui.shipments.geographic_checkpoints', ['count' => count($mapPoints)]) }}</span>
            </div>
        </div>

        <!-- Live Meteorological Conditions -->
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E6EAF0]">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ __('ui.shipments.weather_title') }}</h3>
                        <p class="text-[11px] text-slate-400">{{ __('ui.shipments.weather_subtitle') }}</p>
                    </div>
                    <span class="text-2xl">{{ $weather['icon'] ?? '🌤️' }}</span>
                </div>

                @if($weather)
                    <div class="mt-4 space-y-3 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-[#E6EAF0]">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ __('ui.shipments.telemetry_location') }}</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $weather['location'] }}</span>
                            <span class="text-[10px] text-[#F97316] font-medium">{{ __('ui.shipments.source_label') }} {{ $weather['source'] }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-center">
                            <div class="bg-[#FFF7ED]/50 p-2.5 rounded-xl border border-[#FDBA74]/40">
                                <span class="text-[10px] font-bold text-[#172033] uppercase block">{{ __('ui.shipments.ambient_temp') }}</span>
                                <span class="text-lg font-black text-[#F97316]">{{ $weather['temperature'] }}{{ $weather['temperature_unit'] }}</span>
                            </div>
                            <div class="bg-blue-50/50 p-2.5 rounded-xl border border-blue-100">
                                <span class="text-[10px] font-bold text-blue-800 uppercase block">{{ __('ui.shipments.wind_velocity') }}</span>
                                <span class="text-lg font-black text-blue-900">{{ $weather['wind_speed'] }} <span class="text-xs font-normal">{{ $weather['wind_speed_unit'] }}</span></span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl {{ $weather['is_adverse'] ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-200' }}">
                            <div class="font-bold flex items-center justify-between">
                                <span>{{ __('ui.shipments.condition_label') }} {{ $weather['condition'] }}</span>
                                <span class="text-[10px] uppercase px-1.5 py-0.5 rounded {{ $weather['is_adverse'] ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-900' }}">
                                    {{ $weather['is_adverse'] ? __('ui.common.caution') : __('ui.common.normal') }}
                                </span>
                            </div>
                            <p class="text-[11px] mt-1 opacity-80">
                                {{ $weather['is_adverse'] ? __('ui.shipments.adverse_weather_desc') : __('ui.shipments.normal_weather_desc') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400">
                        {{ __('ui.shipments.weather_unavailable') }}
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-[#E6EAF0] text-[10px] text-slate-400 text-center space-y-0.5">
                <div>{{ __('ui.shipments.weather_footer') }}</div>
                <div class="italic text-slate-400/90">{{ __('ui.shipments.weather_disclaimer') }}</div>
            </div>
        </div>

    </div>

    <!-- Dual Lower Section: Tracking Timeline & Incident/Vehicle Manifest -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Tracking Timeline (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E6EAF0] shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('ui.shipments.timeline_title') }}</h3>
                    <p class="text-xs text-slate-400">{{ __('ui.shipments.timeline_subtitle') }}</p>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ __('ui.shipments.checkpoints_count', ['count' => $shipment->trackingEvents->count()]) }}
                </span>
            </div>

            <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($shipment->trackingEvents as $index => $event)
                    @php
                        $isLatest = ($index === $shipment->trackingEvents->count() - 1);
                    @endphp
                    <div class="relative text-xs">
                        <div class="absolute -left-6 top-1 w-5 h-5 rounded-full border-2 border-white {{ $isLatest ? 'bg-[#F97316] shadow-md shadow-[#F97316]/40 animate-pulse' : ($event->is_exception ? 'bg-rose-500' : 'bg-slate-400') }}"></div>

                        <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/60 {{ $isLatest ? 'ring-2 ring-[#F97316]/20 bg-[#FFF7ED]/50' : '' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2 flex-wrap">
                                    <span>{{ $event->location_name }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $event->is_exception ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $event->event_type }} &bull; {{ __('ui.status.' . $event->status) }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-mono text-slate-500 font-semibold">
                                    {{ $event->event_time->format('M d, Y H:i') }}
                                </span>
                            </div>

                            <p class="text-slate-600 mt-1">
                                {{ $event->description }}
                            </p>

                            <div class="mt-2 pt-2 border-t border-slate-200/40 flex items-center justify-between text-[10px] text-slate-400">
                                <span>{{ __('ui.shipments.logged_by') }} {{ $event->creator->name ?? __('ui.shipments.system_process') }}</span>
                                @if($event->latitude && $event->longitude)
                                    <span class="font-mono">{{ __('ui.shipments.fix_label') }} {{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-400 text-xs py-4">{{ __('ui.shipments.no_checkpoints') }}</div>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Incidents & Vehicle Manifest -->
        <div class="space-y-6">

            <!-- Exceptions & Incidents Card -->
            <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-[#E6EAF0]">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('ui.shipments.incidents_title') }}</h3>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                        {{ __('ui.shipments.logged_count', ['count' => $shipment->exceptions->count()]) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->exceptions as $exc)
                        <div class="p-3.5 rounded-xl border text-xs {{ $exc->isOpen() ? 'bg-rose-50/60 border-rose-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center justify-between font-bold">
                                <span class="text-slate-900">{{ $exc->exception_type }}</span>
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded {{ $exc->status === 'OPEN' ? 'bg-rose-500 text-white' : ($exc->status === 'INVESTIGATING' ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white') }}">
                                    {{ __('ui.status.' . $exc->status) }}
                                </span>
                            </div>

                            <p class="text-slate-600 mt-1">
                                {{ $exc->description }}
                            </p>

                            <div class="mt-2 text-[10px] text-slate-400 flex items-center justify-between">
                                <span>{{ __('ui.shipments.detected_label') }} {{ $exc->detected_at->format('M d, H:i') }}</span>
                                <span class="font-bold text-amber-700">{{ __('ui.shipments.delay_impact', ['hours' => $exc->delay_impact_hours]) }}</span>
                            </div>

                            @if($exc->isResolved())
                                <div class="mt-2 pt-2 border-t border-slate-200 text-[10px] text-emerald-800 bg-emerald-50 p-2 rounded">
                                    <strong>{{ __('ui.shipments.resolved_label') }}</strong> {{ $exc->resolution_notes }}
                                    <div class="text-[9px] text-slate-400 mt-0.5">{{ __('ui.shipments.resolved_by_on', ['name' => $exc->resolver->name ?? 'Ops', 'date' => $exc->resolved_at?->format('M d, H:i')]) }}</div>
                                </div>
                            @elseif(Auth::user()->canManageShipments())
                                <div class="mt-2 pt-2 border-t border-rose-200 flex items-center justify-end gap-2">
                                    @if($exc->status === 'OPEN')
                                        <form method="POST" action="{{ route('exceptions.investigate', $exc->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold transition">
                                                {{ __('ui.dashboard.investigate') }}
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" onclick="openResolveModal('{{ $exc->id }}', '{{ $exc->exception_type }}')"
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[10px] font-bold transition">
                                        {{ __('ui.dashboard.resolve') }} &rarr;
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            {{ __('ui.shipments.no_exceptions') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Vehicle Items Manifest -->
            <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('ui.shipments.manifest_title') }}</h3>
                    <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                        {{ $summary['total_vehicles'] ?? 0 }} {{ __('ui.common.units') }}
                    </span>
                </div>
                <div class="space-y-2.5">
                    @forelse($shipment->items as $item)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-bold text-slate-900">{{ $item->vehicle_model ?? $item->item_description }}</div>
                                <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 shrink-0">{{ $item->cargo_category }}</span>
                            </div>
                            @if($item->vin_number)
                                <div class="text-[11px] font-mono text-slate-600">VIN: <strong class="text-slate-800">{{ $item->vin_number }}</strong></div>
                            @endif
                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                                <span>{{ __('ui.common.qty') }}: <strong>{{ $item->quantity }}</strong></span>
                                <span>{{ number_format($item->weight_kg) }} kg</span>
                                <span class="font-semibold text-emerald-700">${{ number_format($item->declared_value ?? 0) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400">{{ __('ui.shipments.no_units') }}</div>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-[#E6EAF0] space-y-1 text-xs font-bold text-slate-800">
                    <div class="flex items-center justify-between">
                        <span>{{ __('ui.shipments.total_weight_colon') }}</span>
                        <span>{{ number_format($summary['total_weight_kg'] ?? $shipment->total_weight_kg) }} KG</span>
                    </div>
                    <div class="flex items-center justify-between text-emerald-700">
                        <span>{{ __('ui.shipments.total_value_colon') }}</span>
                        <span>${{ number_format($summary['total_declared_value'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Operator Modal 1: Add Tracking Checkpoint -->
<div id="checkpointModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-bold text-slate-900">{{ __('ui.shipments.modal_checkpoint_title') }}</h3>
            <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('tracking.store', $shipment->id) }}" class="space-y-3">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.target_status_label') }}</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                    <option value="{{ $shipment->status }}" selected>{{ __('ui.shipments.keep_current_status', ['status' => __('ui.status.' . $shipment->status)]) }}</option>
                    @foreach($allowedTransitions as $trans)
                        <option value="{{ $trans }}">{{ __('ui.shipments.advance_to', ['status' => __('ui.status.' . $trans)]) }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-400">{{ __('ui.shipments.terminal_hint') }}</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.event_type_label') }}</label>
                <input type="text" name="event_type" required value="IN_TRANSIT"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"
                       placeholder="{{ __('ui.shipments.event_type_placeholder') }}">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.predefined_hub') }}</label>
                    <select name="location_id" id="locSelect" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" onchange="autoFillCoords(this)">
                        <option value="">{{ __('ui.shipments.custom_location') }}</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" data-name="{{ $l->name }}" data-lat="{{ $l->latitude }}" data-lng="{{ $l->longitude }}">{{ $l->code }} &bull; {{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.location_name') }}</label>
                    <input type="text" name="location_name" id="locName" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="{{ __('ui.shipments.checkpoint_name_placeholder') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.latitude') }}</label>
                    <input type="number" step="0.0000001" name="latitude" id="locLat" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. 1.2640">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700">{{ __('ui.shipments.longitude') }}</label>
                        <button type="button" id="checkpointGeocodeBtn" onclick="lookupCheckpointCoords()" class="text-[10px] font-bold text-[#F97316] hover:text-[#EA580C] underline">
                            {{ __('ui.shipments.lookup_coords_btn') }}
                        </button>
                    </div>
                    <input type="number" step="0.0000001" name="longitude" id="locLng" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. 103.8220">
                </div>
            </div>
            <p id="checkpointGeocodeMsg" class="hidden text-[10px] text-amber-600 font-medium"></p>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.checkpoint_timestamp') }}</label>
                <input type="datetime-local" name="event_time" required value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.operator_notes_label') }}</label>
                <textarea name="description" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="{{ __('ui.shipments.operator_notes_placeholder') }}"></textarea>
            </div>

            <label class="flex items-center gap-2 text-slate-700 font-semibold cursor-pointer">
                <input type="checkbox" name="is_exception" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                <span>{{ __('ui.shipments.mark_as_exception') }}</span>
            </label>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold">{{ __('ui.shipments.record_checkpoint') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Operator Modal 2: Log Exception -->
<div id="exceptionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-bold text-rose-900">{{ __('ui.shipments.modal_exception_title') }}</h3>
            <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('exceptions.store', $shipment->id) }}" class="space-y-3">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.incident_category') }}</label>
                <select name="exception_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                    @foreach($exceptionTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.severity_field') }}</label>
                    <select name="severity" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="LOW">{{ __('ui.shipments.sev_low') }}</option>
                        <option value="MEDIUM" selected>{{ __('ui.shipments.sev_medium') }}</option>
                        <option value="HIGH">{{ __('ui.shipments.sev_high') }}</option>
                        <option value="CRITICAL">{{ __('ui.shipments.sev_critical') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.estimated_delay_hours') }}</label>
                    <input type="number" name="delay_impact_hours" value="12" min="0" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.detection_datetime') }}</label>
                <input type="datetime-local" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.incident_narrative') }}</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="{{ __('ui.shipments.incident_narrative_placeholder') }}"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold">{{ __('ui.shipments.flag_exception_btn') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Operator Modal 3: Resolve Exception -->
<div id="resolveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-bold text-emerald-900">{{ __('ui.shipments.modal_resolve_title') }}</h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form id="resolveForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PATCH')

            <p class="text-slate-600">
                {{ __('ui.shipments.modal_resolve_prefix') }} <strong id="resolveTypeLabel"></strong> {{ __('ui.shipments.modal_resolve_suffix') }}
            </p>

            <div>
                <label class="block font-bold text-slate-700 mb-1">{{ __('ui.shipments.resolution_protocol_label') }}</label>
                <textarea name="resolution_notes" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="{{ __('ui.shipments.resolution_protocol_placeholder') }}"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">{{ __('ui.dashboard.confirm_resolution') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@php
    $showI18n = [
        'location' => __('ui.shipments.map_popup_location'),
        'status' => __('ui.shipments.map_popup_status'),
        'time' => __('ui.shipments.map_popup_time'),
    ];
@endphp
<script>
const SHOW_I18N = @json($showI18n);
const GEOCODE_URL = @json(route('geocode.lookup'));
const GEOCODE_I18N = {
    btn: @json(__('ui.shipments.lookup_coords_btn')),
    loading: @json(__('ui.shipments.lookup_coords_loading')),
    failed: @json(__('ui.shipments.lookup_coords_failed')),
};

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function lookupCheckpointCoords() {
    const query = (document.getElementById('locName')?.value || '').trim();
    const btn = document.getElementById('checkpointGeocodeBtn');
    const msg = document.getElementById('checkpointGeocodeMsg');
    if (query.length < 2 || !btn) return;

    btn.disabled = true;
    btn.innerText = GEOCODE_I18N.loading;
    if (msg) msg.classList.add('hidden');

    try {
        const res = await fetch(`${GEOCODE_URL}?q=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.success && json.data && json.data.latitude !== null && json.data.longitude !== null) {
            document.getElementById('locLat').value = json.data.latitude;
            document.getElementById('locLng').value = json.data.longitude;
        } else if (msg) {
            msg.innerText = json.data?.message || GEOCODE_I18N.failed;
            msg.classList.remove('hidden');
        }
    } catch (err) {
        if (msg) {
            msg.innerText = GEOCODE_I18N.failed;
            msg.classList.remove('hidden');
        }
    } finally {
        btn.disabled = false;
        btn.innerText = GEOCODE_I18N.btn;
    }
}

function autoFillCoords(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('locName').value = option.dataset.name;
        document.getElementById('locLat').value = option.dataset.lat;
        document.getElementById('locLng').value = option.dataset.lng;
    }
}

function openResolveModal(id, type) {
    document.getElementById('resolveTypeLabel').innerText = type;
    document.getElementById('resolveForm').action = '/exceptions/' + id + '/resolve';
    document.getElementById('resolveModal').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    const mapPoints = @json($mapPoints);
    if (mapPoints.length === 0) return;

    const first = mapPoints[0];
    const map = L.map('shipmentMap').setView([first.lat, first.lng], 4);

    const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
    }).addTo(map);

    const tileErrorEl = document.getElementById('shipmentMapTileError');
    tiles.on('tileerror', () => tileErrorEl?.classList.remove('hidden'));
    tiles.on('load', () => tileErrorEl?.classList.add('hidden'));

    const latLngs = [];
    const isAir = @json($shipment->carrier->service_type === 'AIR_FREIGHT');
    const vehicleEmoji = isAir ? '✈️' : '🚢';

    const createIcon = (color, symbol) => L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${color}; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">${symbol}</div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });

    mapPoints.forEach((pt, idx) => {
        latLngs.push([pt.lat, pt.lng]);

        let icon = createIcon('#F97316', vehicleEmoji);
        if (pt.type === 'origin') icon = createIcon('#10A37F', 'O');
        if (pt.type === 'destination') icon = createIcon('#F04452', 'D');
        if (pt.type === 'exception') icon = createIcon('#F04452', '!');

        const marker = L.marker([pt.lat, pt.lng], { icon: icon }).addTo(map);
        marker.bindPopup(`
            <div style="font-size: 12px; font-family: inherit; min-width: 190px;">
                <strong style="color: #0f172a;">${escapeHtml(pt.name)}</strong><br/>
                <span style="color: #64748b;">${escapeHtml(SHOW_I18N.location)} ${escapeHtml(pt.city)}</span><br/>
                <span style="color: #0f172a; font-weight: 700;">${escapeHtml(SHOW_I18N.status)} ${escapeHtml(pt.status || 'CHECKPOINT')}</span><br/>
                <span style="color: #F97316; font-weight: 600;">${escapeHtml(SHOW_I18N.time)} ${escapeHtml(pt.time)}</span>
                ${pt.desc ? `<p style="margin-top: 4px; color: #334155;">${escapeHtml(pt.desc)}</p>` : ''}
            </div>
        `);
    });

    if (latLngs.length > 1) {
        const polyline = L.polyline(latLngs, {
            color: '#F97316',
            weight: 3.5,
            dashArray: '6, 8',
            opacity: 0.85
        }).addTo(map);

        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    }
});
</script>
@endpush
