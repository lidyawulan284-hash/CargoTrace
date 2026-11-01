@extends('layouts.app')

@section('title', 'Shipment ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">

    <!-- Header Navigation & Status Ribbon -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight font-mono">
                        {{ $shipment->shipment_number }}
                    </h1>
                    
                    @php
                        $badge = match($shipment->status) {
                            'CREATED' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'BOOKED' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'PICKED_UP' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'IN_TRANSIT' => 'bg-sky-50 text-sky-700 border-sky-300 font-bold animate-pulse',
                            'DELIVERED' => 'bg-emerald-50 text-emerald-700 border-emerald-300 font-bold',
                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $badge }}">
                        {{ $shipment->status }}
                    </span>

                    @if($shipment->is_delayed)
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1">
                            <span>⚠️ DELAYED +{{ $shipment->delay_hours }}H</span>
                            <span class="text-[9px] bg-amber-300 text-amber-950 px-1.5 py-0.2 rounded">{{ $shipment->delay_severity }}</span>
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ✓ ON SCHEDULE
                        </span>
                    @endif
                </div>

                <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span>Shipper: <strong class="text-slate-800">{{ $shipment->customer->company_name ?? $shipment->customer->name }}</strong></span>
                    <span>&bull;</span>
                    <span>Carrier: <strong class="text-slate-800">{{ $shipment->carrier->name }}</strong> ({{ $shipment->carrier->code }})</span>
                    <span>&bull;</span>
                    <span>Route: <strong class="text-slate-800">{{ $shipment->origin->city }}</strong> &rarr; <strong class="text-slate-800">{{ $shipment->destination->city }}</strong></span>
                </div>
            </div>

            <!-- Operator Action Buttons (Slide 5) -->
            @if(Auth::user()->canManageShipments())
                <div class="flex items-center space-x-2">
                    @if(!$shipment->isTerminal())
                        <button type="button" onclick="document.getElementById('checkpointModal').classList.remove('hidden')" 
                                class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Checkpoint
                        </button>
                        <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Flag Incident
                        </button>
                    @endif
                    <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                        Edit
                    </a>
                </div>
            @endif

        </div>

        <!-- Schedule & Deterministic Delay Bar (Slide 8) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Scheduled Departure</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $shipment->expected_departure->format('M d, Y H:i') }}</span>
                <span class="text-[10px] text-slate-400">Actual: {{ $shipment->actual_departure ? $shipment->actual_departure->format('M d, H:i') : 'Pending' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Scheduled Arrival (Baseline)</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $shipment->expected_arrival->format('M d, Y H:i') }}</span>
                <span class="text-[10px] text-slate-400">Target SLA destination delivery</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Dynamic ETA Forecast</span>
                <span class="font-bold {{ $shipment->is_delayed ? 'text-amber-600' : 'text-emerald-600' }} text-sm mt-0.5 block">
                    {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('M d, Y H:i') : $shipment->expected_arrival->format('M d, Y H:i') }}
                </span>
                <span class="text-[10px] text-slate-400">Calculated by CargoTrace EtaEngine</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Delay Discrepancy & Severity</span>
                @if($shipment->is_delayed)
                    <div class="mt-0.5">
                        <span class="font-black text-amber-700 text-sm">+{{ $shipment->delay_hours }} Hours Late</span>
                        <span class="text-[10px] block text-amber-600 font-bold">Severity: {{ $shipment->delay_severity }}</span>
                    </div>
                @else
                    <span class="font-bold text-emerald-600 text-sm mt-0.5 block">0 Hours (Nominal)</span>
                    <span class="text-[10px] text-emerald-600">Within acceptable transit SLA</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Map & Weather Grid (Slide 9: Leaflet.js + OpenStreetMap + Open-Meteo) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Interactive Map (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex flex-col">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-ping"></span>
                    <h3 class="text-sm font-bold text-slate-900">Live Spatial Route & Checkpoints</h3>
                </div>
                <span class="text-[10px] text-slate-400">Leaflet.js &bull; OpenStreetMap Tiles</span>
            </div>
            
            <div id="shipmentMap" class="w-full h-80 rounded-xl border border-slate-200 z-10 flex-1"></div>
            
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 px-2">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Origin</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span> Intermediate</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> Destination</span>
                </div>
                <span>{{ count($mapPoints) }} geographic checkpoints</span>
            </div>
        </div>

        <!-- Live Meteorological Conditions (Slide 9: External API Weather) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Atmospheric Telemetry</h3>
                        <p class="text-[11px] text-slate-400">External Live API Integration</p>
                    </div>
                    <span class="text-2xl">{{ $weather['icon'] ?? '🌤️' }}</span>
                </div>

                @if($weather)
                    <div class="mt-4 space-y-3 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Telemetry Location</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $weather['location'] }}</span>
                            <span class="text-[10px] text-sky-600 font-medium">Source: {{ $weather['source'] }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-center">
                            <div class="bg-sky-50/50 p-2.5 rounded-xl border border-sky-100">
                                <span class="text-[10px] font-bold text-sky-800 uppercase block">Ambient Temp</span>
                                <span class="text-lg font-black text-sky-900">{{ $weather['temperature'] }}{{ $weather['temperature_unit'] }}</span>
                            </div>
                            <div class="bg-blue-50/50 p-2.5 rounded-xl border border-blue-100">
                                <span class="text-[10px] font-bold text-blue-800 uppercase block">Wind Velocity</span>
                                <span class="text-lg font-black text-blue-900">{{ $weather['wind_speed'] }} <span class="text-xs font-normal">{{ $weather['wind_speed_unit'] }}</span></span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl {{ $weather['is_adverse'] ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-200' }}">
                            <div class="font-bold flex items-center justify-between">
                                <span>Condition: {{ $weather['condition'] }}</span>
                                <span class="text-[10px] uppercase px-1.5 py-0.5 rounded {{ $weather['is_adverse'] ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-900' }}">
                                    {{ $weather['is_adverse'] ? 'Caution' : 'Normal' }}
                                </span>
                            </div>
                            <p class="text-[11px] mt-1 opacity-80">
                                {{ $weather['is_adverse'] ? 'Adverse meteorological conditions detected; transit speeds may experience variance.' : 'Weather parameters optimal for transit navigation.' }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400">
                        Weather telemetry unavailable for this coordinate fix.
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-[10px] text-slate-400 text-center">
                Refreshed dynamically via Open-Meteo REST service
            </div>
        </div>

    </div>

    <!-- Dual Lower Section: Tracking Timeline & Incident/Cargo Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Tracking Timeline (Slide 7: Historical Event Sequence) (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Historical Tracking Chronology</h3>
                    <p class="text-xs text-slate-400">Strictly ordered append-only checkpoint trail</p>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $shipment->trackingEvents->count() }} Checkpoints
                </span>
            </div>

            <!-- Vertical Timeline -->
            <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($shipment->trackingEvents as $index => $event)
                    @php
                        $isLatest = ($index === $shipment->trackingEvents->count() - 1);
                    @endphp
                    <div class="relative text-xs">
                        <!-- Dot Icon -->
                        <div class="absolute -left-6 top-1 w-5 h-5 rounded-full border-2 border-white {{ $isLatest ? 'bg-sky-500 shadow-md shadow-sky-500/50 animate-pulse' : ($event->is_exception ? 'bg-rose-500' : 'bg-slate-400') }}"></div>

                        <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/60 {{ $isLatest ? 'ring-2 ring-sky-500/20 bg-sky-50/20' : '' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $event->location_name }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $event->is_exception ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $event->event_type }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-mono text-slate-500 font-semibold">
                                    {{ $event->event_time->format('M d, Y &bull; H:i') }}
                                </span>
                            </div>

                            <p class="text-slate-600 mt-1">
                                {{ $event->description }}
                            </p>

                            <div class="mt-2 pt-2 border-t border-slate-200/40 flex items-center justify-between text-[10px] text-slate-400">
                                <span>Logged by: {{ $event->creator->name ?? 'System Process' }}</span>
                                @if($event->latitude && $event->longitude)
                                    <span class="font-mono">Fix: {{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-400 text-xs py-4">No tracking checkpoints registered.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Incidents & Cargo Manifest (1 col) -->
        <div class="space-y-6">

            <!-- Exceptions & Incidents Card (Slide 8) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Incident & Exceptions Log</h3>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                        {{ $shipment->exceptions->count() }} Logged
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->exceptions as $exc)
                        <div class="p-3.5 rounded-xl border text-xs {{ $exc->isOpen() ? 'bg-rose-50/60 border-rose-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center justify-between font-bold">
                                <span class="text-slate-900">{{ $exc->exception_type }}</span>
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded {{ $exc->isOpen() ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white' }}">
                                    {{ $exc->status }}
                                </span>
                            </div>

                            <p class="text-slate-600 mt-1">
                                {{ $exc->description }}
                            </p>

                            <div class="mt-2 text-[10px] text-slate-400 flex items-center justify-between">
                                <span>Detected: {{ $exc->detected_at->format('M d, H:i') }}</span>
                                <span class="font-bold text-amber-700">+{{ $exc->delay_impact_hours }}h Delay Impact</span>
                            </div>

                            @if($exc->isResolved())
                                <div class="mt-2 pt-2 border-t border-slate-200 text-[10px] text-emerald-800 bg-emerald-50 p-2 rounded">
                                    <strong>Resolved:</strong> {{ $exc->resolution_notes }}
                                    <div class="text-[9px] text-slate-400 mt-0.5">By {{ $exc->resolver->name ?? 'Ops' }} on {{ $exc->resolved_at?->format('M d, H:i') }}</div>
                                </div>
                            @elseif(Auth::user()->canManageShipments())
                                <div class="mt-2 pt-2 border-t border-rose-200 text-right">
                                    <button type="button" onclick="openResolveModal('{{ $exc->id }}', '{{ $exc->exception_type }}')" 
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[10px] font-bold transition">
                                        Resolve Incident &rarr;
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            ✨ No exceptions logged for this consignment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cargo Specifications Manifest (Slide 11) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Cargo Items Manifest</h3>
                <div class="space-y-2">
                    @forelse($shipment->items as $item)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
                            <div class="font-bold text-slate-800">{{ $item->item_description }}</div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                                <span>Qty: {{ $item->quantity }} pcs</span>
                                <span>{{ number_format($item->weight_kg) }} kg</span>
                                <span class="font-mono text-[10px] text-slate-400">HS: {{ $item->hs_code ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400">No individual items registered.</div>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-800">
                    <span>Total Weight:</span>
                    <span>{{ number_format($shipment->total_weight_kg) }} KG</span>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Operator Modal 1: Add Tracking Checkpoint (Slide 7) -->
<div id="checkpointModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Log Tracking Checkpoint</h3>
            <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('tracking.store', $shipment->id) }}" class="space-y-3">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Target Shipment Status (Validated by Transition Rules)</label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                    <option value="{{ $shipment->status }}" selected>Keep Current Status ({{ $shipment->status }})</option>
                    @foreach($allowedTransitions as $trans)
                        <option value="{{ $trans }}">Advance to &rarr; {{ $trans }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-400">Terminal statuses like DELIVERED cannot revert (Slide 6).</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Event Type / Activity</label>
                <input type="text" name="event_type" required value="IN_TRANSIT"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"
                       placeholder="e.g. BERTHED, CUSTOMS_CLEARED, DEPARTED_PORT">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Predefined Location</label>
                    <select name="location_id" id="locSelect" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" onchange="autoFillCoords(this)">
                        <option value="">Custom Location...</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}" data-name="{{ $l->name }}" data-lat="{{ $l->latitude }}" data-lng="{{ $l->longitude }}">{{ $l->name }} ({{ $l->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Location Name</label>
                    <input type="text" name="location_name" id="locName" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="Checkpoint Name">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" id="locLat" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. 1.290270">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" id="locLng" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. 103.851959">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Checkpoint Timestamp</label>
                <input type="datetime-local" name="event_time" required value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Operator Notes / Log Description</label>
                <textarea name="description" rows="2" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="Container loaded onto feeder vessel..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold">Record Checkpoint</button>
            </div>
        </form>
    </div>
</div>

<!-- Operator Modal 2: Log Exception (Slide 8) -->
<div id="exceptionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-rose-900">Flag Transit Exception / Delay Alert</h3>
            <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('exceptions.store', $shipment->id) }}" class="space-y-3">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Incident Category (Slide 8)</label>
                <select name="exception_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                    @foreach($exceptionTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Severity</label>
                    <select name="severity" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="LOW">LOW (0 - 6 hrs impact)</option>
                        <option value="MEDIUM" selected>MEDIUM (6 - 24 hrs impact)</option>
                        <option value="HIGH">HIGH (> 24 hrs impact)</option>
                        <option value="CRITICAL">CRITICAL (Vessel breakdown/quarantine)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Estimated Delay Impact (Hours)</label>
                    <input type="number" name="delay_impact_hours" value="18" min="0" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Detection Date & Time</label>
                <input type="datetime-local" name="detected_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Incident Narrative & Operational Notes</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="Describe the cause of delay (customs inspection, heavy swells, berth unavailability)..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold">Flag Exception</button>
            </div>
        </form>
    </div>
</div>

<!-- Operator Modal 3: Resolve Exception -->
<div id="resolveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-emerald-900">Clear & Resolve Incident</h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form id="resolveForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PATCH')

            <p class="text-slate-600">
                You are marking incident <strong id="resolveTypeLabel"></strong> as resolved.
            </p>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Resolution Clearance Protocol Notes</label>
                <textarea name="resolution_notes" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. Customs physical examination passed; release order granted..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">Confirm Resolution</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto fill coordinates when picking a location from the dropdown
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

// Leaflet.js Map Initialization (Slide 9)
document.addEventListener('DOMContentLoaded', function () {
    const mapPoints = @json($mapPoints);

    if (mapPoints.length === 0) {
        return;
    }

    // Default center to first point
    const first = mapPoints[0];
    const map = L.map('shipmentMap').setView([first.lat, first.lng], 4);

    // OpenStreetMap Tile Layer with proper attribution (Slide 9)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const latLngs = [];

    // Custom Icons
    const createIcon = (color, symbol) => L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${color}; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 11px; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">${symbol}</div>`,
        iconSize: [26, 26],
        iconAnchor: [13, 13]
    });

    mapPoints.forEach((pt, index) => {
        latLngs.push([pt.lat, pt.lng]);
        
        let icon = createIcon('#0284c7', '•');
        if (pt.type === 'origin') icon = createIcon('#10b981', 'O');
        if (pt.type === 'destination') icon = createIcon('#ef4444', 'D');
        if (pt.type === 'exception') icon = createIcon('#f43f5e', '!');

        const marker = L.marker([pt.lat, pt.lng], { icon: icon }).addTo(map);
        marker.bindPopup(`
            <div style="font-size: 12px; font-family: inherit;">
                <strong style="color: #0f172a;">${pt.name}</strong><br/>
                <span style="color: #64748b;">${pt.city}</span><br/>
                <span style="color: #0284c7; font-weight: 600;">Time: ${pt.time}</span>
                ${pt.desc ? `<p style="margin-top: 4px; color: #334155;">${pt.desc}</p>` : ''}
            </div>
        `);
    });

    // Draw Route Polyline
    if (latLngs.length > 1) {
        const polyline = L.polyline(latLngs, {
            color: '#0284c7',
            weight: 3.5,
            dashArray: '6, 8',
            opacity: 0.8
        }).addTo(map);

        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    }
});
</script>
@endpush
