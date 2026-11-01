@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Operations Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time shipment telematics & logistics metrics</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(Auth::user()->canManageShipments())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-500/25 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Shipment
                </a>
            @endif
        </div>
    </div>

    <!-- Top KPI Stat Cards (Slide 10) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <!-- Total Shipments -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    📦
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $metrics['total_shipments'] }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Recorded Bookings</div>
            </div>
        </div>

        <!-- In Transit -->
        <div class="bg-white p-4 rounded-2xl border border-blue-200/80 shadow-sm flex flex-col justify-between bg-gradient-to-b from-white to-blue-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">In Transit</span>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    🚢
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-blue-600">{{ $metrics['in_transit'] }}</div>
                <div class="text-[11px] text-blue-500 mt-0.5">Active Cargo Flow</div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-sm flex flex-col justify-between bg-gradient-to-b from-white to-emerald-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Delivered</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    ✅
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-600">{{ $metrics['delivered'] }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-0.5">Completed Voyages</div>
            </div>
        </div>

        <!-- Delayed (Slide 8 rule) -->
        <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-sm flex flex-col justify-between bg-gradient-to-b from-white to-amber-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Delayed</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    ⚠️
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-amber-600">{{ $metrics['delayed'] }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-0.5">{{ $metrics['delay_rate'] }}% of volume</div>
            </div>
        </div>

        <!-- Open Exceptions -->
        <div class="bg-white p-4 rounded-2xl border border-rose-200/80 shadow-sm flex flex-col justify-between bg-gradient-to-b from-white to-rose-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Exceptions</span>
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600">
                    🚨
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-rose-600">{{ $metrics['open_exceptions'] }}</div>
                <div class="text-[11px] text-rose-600 font-medium mt-0.5">Require Clearance</div>
            </div>
        </div>

        <!-- On-Time KPI (Slide 23) -->
        <div class="bg-white p-4 rounded-2xl border border-indigo-200/80 shadow-sm flex flex-col justify-between bg-gradient-to-b from-white to-indigo-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">On-Time KPI</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    🎯
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-indigo-600">{{ $metrics['on_time_rate'] }}%</div>
                <div class="text-[11px] text-indigo-500 mt-0.5">SLA Target &ge; 90%</div>
            </div>
        </div>

    </div>

    <!-- Charts Section (Slide 10 & 14: Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Distribution Doughnut Chart -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Shipment Lifecycle Status</h3>
                <span class="text-[10px] uppercase font-bold text-slate-400">All Voyages</span>
            </div>
            <div class="relative h-60 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Carrier Volume Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 tracking-tight">Shipment Volume by Carrier Line</h3>
                <span class="text-[10px] uppercase font-bold text-slate-400">Distribution</span>
            </div>
            <div class="relative h-60">
                <canvas id="carrierChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Dual Operational Tables (Recent Shipments & Live Activity Stream) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Shipments Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Active Shipments Priority Watch</h3>
                    <p class="text-xs text-slate-400">Recent consignments with live routing telemetry</p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                    <span>View All</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Shipment Ref</th>
                            <th class="px-4 py-3">Carrier / Route</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">ETA Reliability</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                        @forelse($recentShipments as $s)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('shipments.show', $s->id) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                        {{ $s->shipment_number }}
                                    </a>
                                    <span class="text-[11px] text-slate-400">{{ $s->customer->company_name ?? $s->customer->name }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-800">{{ $s->carrier->name }}</div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1">
                                        <span>{{ $s->origin->city }}</span>
                                        <span>&rarr;</span>
                                        <span>{{ $s->destination->city }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $badgeColor = match($s->status) {
                                            'CREATED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'BOOKED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'PICKED_UP' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'IN_TRANSIT' => 'bg-sky-50 text-sky-700 border-sky-200 animate-pulse',
                                            'DELIVERED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badgeColor }}">
                                        {{ $s->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($s->is_delayed)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                            <span>Delayed +{{ $s->delay_hours }}h</span>
                                            <span class="text-[9px] uppercase px-1 rounded bg-amber-200 text-amber-900">{{ $s->delay_severity }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            On Schedule
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('shipments.show', $s->id) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-sky-600 hover:text-white font-semibold text-[11px] text-slate-700 transition">
                                        Track &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                    No shipments available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Active Exceptions & Timeline Feed (1 col) -->
        <div class="space-y-6">

            <!-- Active Exceptions Card (Slide 8) -->
            <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-rose-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        Active Incidents
                    </h3>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                        {{ $activeExceptions->count() }} Open
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($activeExceptions as $exc)
                        <div class="p-3 rounded-xl bg-rose-50/50 border border-rose-100 text-xs">
                            <div class="flex items-center justify-between font-bold text-rose-900">
                                <span>{{ $exc->exception_type }}</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded uppercase {{ $exc->severity === 'CRITICAL' ? 'bg-rose-600 text-white' : 'bg-rose-200 text-rose-800' }}">
                                    {{ $exc->severity }}
                                </span>
                            </div>
                            <div class="text-slate-600 mt-1 line-clamp-2">
                                {{ $exc->description }}
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-rose-100/60 text-[10px] text-slate-400">
                                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="font-bold text-sky-600 hover:underline">
                                    {{ $exc->shipment->shipment_number }}
                                </a>
                                <span>+{{ $exc->delay_impact_hours }}h impact</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            ✨ All shipments operating without active exceptions.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Checkpoint Events Feed (Slide 7) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Live Checkpoint Activity</h3>
                <div class="space-y-3">
                    @forelse($recentEvents as $evt)
                        <div class="flex items-start space-x-3 text-xs">
                            <div class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-[10px] font-bold mt-0.5">
                                •
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-900 truncate">
                                    {{ $evt->location_name }}
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    <a href="{{ route('shipments.show', $evt->shipment_id) }}" class="text-sky-600 hover:underline font-medium">
                                        {{ $evt->shipment->shipment_number }}
                                    </a>: {{ $evt->description }}
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $evt->event_time->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-400">
                            No recent checkpoints.
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
    // 1. Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($metrics['status_chart']);
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#94a3b8', // CREATED (slate)
                    '#3b82f6', // BOOKED (blue)
                    '#6366f1', // PICKED_UP (indigo)
                    '#0284c7', // IN_TRANSIT (sky)
                    '#10b981', // DELIVERED (emerald)
                    '#f43f5e', // CANCELLED (rose)
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 10, family: 'inherit' } }
                }
            },
            cutout: '70%',
        }
    });

    // 2. Carrier Volume Bar Chart
    const carrierCtx = document.getElementById('carrierChart').getContext('2d');
    const carrierData = @json($metrics['carrier_chart']);

    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: carrierData.map(c => c.name),
            datasets: [{
                label: 'Assigned Shipments',
                data: carrierData.map(c => c.count),
                backgroundColor: '#0284c7',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 10 } },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
