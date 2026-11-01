@extends('layouts.app')

@section('title', 'Shipments Directory')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Shipment Tracking Registry</h1>
            <p class="text-xs text-slate-500 mt-0.5">Global consignment inventory, transit schedules, and exception states</p>
        </div>
        @if(Auth::user()->canManageShipments())
            <div>
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-500/25 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Shipment Booking
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Search Toolbar (Slide 10) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 text-xs">
            
            <!-- Keyword Search -->
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-600 mb-1">Search Ref / Customer</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="e.g. CT-2026 or Company..." 
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <!-- Status Dropdown -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Lifecycle Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Shipment::STATUSES as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Carrier Dropdown -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Carrier Line</label>
                <select name="carrier_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ request('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Delay Filter -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Delay State</label>
                <select name="is_delayed" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">All Schedules</option>
                    <option value="1" {{ request('is_delayed') === '1' ? 'selected' : '' }}>⚠️ Delayed Only</option>
                    <option value="0" {{ request('is_delayed') === '0' ? 'selected' : '' }}>✅ On Time Only</option>
                </select>
            </div>

            <!-- Actions (Filter / Reset) -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition">
                    Filter
                </button>
                <a href="{{ route('shipments.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition text-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Shipments Table (Slide 10) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Shipment Number</th>
                        <th class="px-4 py-3.5">Customer / Cargo</th>
                        <th class="px-4 py-3.5">Carrier Line</th>
                        <th class="px-4 py-3.5">Transit Route</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">ETA & Delay State</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                    @forelse($shipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            <!-- Shipment Number -->
                            <td class="px-5 py-4">
                                <a href="{{ route('shipments.show', $s->id) }}" class="font-bold text-slate-900 hover:text-sky-600 text-sm block">
                                    {{ $s->shipment_number }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">ID: CT-{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <!-- Customer & Cargo Type -->
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-800">{{ $s->customer->company_name ?? $s->customer->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $s->cargo_type }} &bull; {{ number_format($s->total_weight_kg) }} kg</div>
                            </td>

                            <!-- Carrier -->
                            <td class="px-4 py-4">
                                <span class="font-semibold text-slate-800">{{ $s->carrier->name }}</span>
                                <span class="text-[10px] block text-slate-400">{{ $s->carrier->code }} &bull; {{ $s->carrier->service_type }}</span>
                            </td>

                            <!-- Route -->
                            <td class="px-4 py-4">
                                <div class="text-[11px] font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span>{{ $s->origin->city }} ({{ $s->origin->code }})</span>
                                    <span class="text-slate-300">&rarr;</span>
                                    <span>{{ $s->destination->city }} ({{ $s->destination->code }})</span>
                                </div>
                                <span class="text-[10px] text-slate-400">Departed: {{ $s->expected_departure->format('M d, Y') }}</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-4">
                                @php
                                    $badge = match($s->status) {
                                        'CREATED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'BOOKED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'PICKED_UP' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'IN_TRANSIT' => 'bg-sky-50 text-sky-700 border-sky-200 font-bold animate-pulse',
                                        'DELIVERED' => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold',
                                        default => 'bg-rose-50 text-rose-700 border-rose-200',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ $s->status }}
                                </span>
                            </td>

                            <!-- ETA & Delay Rule -->
                            <td class="px-4 py-4">
                                @if($s->is_delayed)
                                    <div>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            <span>+{{ $s->delay_hours }}h Late</span>
                                            <span class="text-[9px] uppercase px-1 rounded bg-amber-200 text-amber-900 font-black">{{ $s->delay_severity }}</span>
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Est: {{ $s->estimated_arrival ? $s->estimated_arrival->format('M d, H:i') : '-' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-emerald-600 font-semibold text-[11px]">
                                        <span>On Schedule</span>
                                        <div class="text-[10px] text-slate-400 font-normal">
                                            ETA: {{ $s->expected_arrival->format('M d, Y') }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('shipments.show', $s->id) }}" class="px-2.5 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white font-bold transition">
                                    Details
                                </a>
                                @if(Auth::user()->canManageShipments())
                                    <a href="{{ route('shipments.edit', $s->id) }}" class="px-2 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                                        Edit
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <form method="POST" action="{{ route('shipments.destroy', $s->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                                            &times;
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                No shipments found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($shipments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
