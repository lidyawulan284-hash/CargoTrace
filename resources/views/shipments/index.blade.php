@extends('layouts.app')

@section('title', __('ui.shipments.index_title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.vehicle_shipments') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.shipments.heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.shipments.subheading') }}</p>
        </div>
        @if(Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-xs transition self-start sm:self-auto">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>{{ __('ui.shipments.new_booking') }}</span>
            </a>
        @endif
    </div>

    <!-- 5 KPI Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                <i data-lucide="container" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.dashboard.total_shipments') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['total'] ?? $shipments->total() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#F59E0B] flex items-center justify-center shrink-0">
                <i data-lucide="calendar-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.booked_shipments') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['booked'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center shrink-0">
                <i data-lucide="ship" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.dashboard.in_transit') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['in_transit'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10A37F] flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.dashboard.delivered') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['delivered'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5 col-span-2 sm:col-span-1">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#F04452] flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.exception_shipments') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['exceptions'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Server-Side Filter, Search & Sorting Bar -->
    <div class="bg-white p-5 rounded-2xl border border-[#E6EAF0] shadow-saas">
        <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 text-xs">

            <div class="lg:col-span-2">
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.search_label') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('ui.shipments.search_placeholder') }}"
                       class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none focus:ring-2 focus:ring-[#F97316]/15">
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.common.status') }}</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="">{{ __('ui.shipments.all_statuses') }}</option>
                    @foreach(\App\Models\Shipment::STATUSES as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ __('ui.status.' . $st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.carrier') }}</label>
                <select name="carrier_id" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="">{{ __('ui.shipments.all_carriers') }}</option>
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ (string) request('carrier_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.origin_hub') }}</label>
                <select name="origin_id" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="">{{ __('ui.shipments.all_origins') }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (string) request('origin_id') === (string) $loc->id ? 'selected' : '' }}>{{ $loc->code }} &bull; {{ $loc->city }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.destination_hub') }}</label>
                <select name="destination_id" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="">{{ __('ui.shipments.all_destinations') }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (string) request('destination_id') === (string) $loc->id ? 'selected' : '' }}>{{ $loc->code }} &bull; {{ $loc->city }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.delay_state') }}</label>
                <select name="is_delayed" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="">{{ __('ui.shipments.all_schedules') }}</option>
                    <option value="1" {{ request('is_delayed') === '1' ? 'selected' : '' }}>{{ __('ui.shipments.delayed_only') }}</option>
                    <option value="0" {{ request('is_delayed') === '0' ? 'selected' : '' }}>{{ __('ui.shipments.on_time_only') }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white rounded-xl font-bold transition cursor-pointer">
                    {{ __('ui.common.filter') }}
                </button>
                <a href="{{ route('shipments.index') }}" class="py-2 px-3 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] rounded-xl font-semibold transition text-center">
                    {{ __('ui.common.reset') }}
                </a>
            </div>

            <div class="lg:col-span-2">
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.departure_from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <div class="lg:col-span-2">
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.arrival_to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <div class="lg:col-span-2">
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.sort_by') }}</label>
                <select name="sort_by" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>{{ __('ui.shipments.sort_newest') }}</option>
                    <option value="shipment_number" {{ request('sort_by') === 'shipment_number' ? 'selected' : '' }}>{{ __('ui.shipments.sort_number') }}</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>{{ __('ui.shipments.sort_status') }}</option>
                    <option value="expected_arrival" {{ request('sort_by') === 'expected_arrival' ? 'selected' : '' }}>{{ __('ui.shipments.sort_eta') }}</option>
                    <option value="delay_hours" {{ request('sort_by') === 'delay_hours' ? 'selected' : '' }}>{{ __('ui.shipments.sort_delay') }}</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.sort_dir') }}</label>
                <select name="sort_dir" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>{{ __('ui.shipments.sort_desc') }}</option>
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>{{ __('ui.shipments.sort_asc') }}</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Shipments Data Table -->
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F5F9] text-[#718096] uppercase text-[10px] font-bold tracking-wider border-b border-[#E6EAF0]">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('ui.shipments.col_number') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.shipments.col_customer_vehicles') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.dashboard.origin') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.dashboard.destination') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.shipments.col_carrier') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.shipments.col_eta_delay') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.common.status') }}</th>
                        <th class="px-5 py-3.5 text-right">{{ __('ui.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6EAF0] font-medium text-[#172033]">
                    @forelse($shipments as $s)
                        <tr class="hover:bg-[#F4F5F9]/60 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('shipments.show', $s->id) }}" class="font-extrabold text-[#172033] hover:text-[#F97316] text-sm block font-mono transition">
                                    {{ $s->shipment_number }}
                                </a>
                                <span class="text-[10px] text-[#718096] font-mono">{{ $s->cargo_type }} &bull; {{ $s->items->sum('quantity') }} {{ __('ui.common.units_lower') }}</span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-bold text-[#172033]">{{ $s->customer->company_name ?? $s->customer->name }}</div>
                                <div class="text-[11px] text-[#718096] truncate max-w-[200px]">
                                    {{ $s->items->pluck('vehicle_model')->filter()->implode(', ') ?: $s->cargo_type }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold text-[#172033]">{{ $s->origin->city }}</div>
                                <span class="text-[10px] text-[#718096] font-mono">{{ $s->origin->code }} &bull; {{ $s->expected_departure->format('M d, Y') }}</span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold text-[#172033]">{{ $s->destination->city }}</div>
                                <span class="text-[10px] text-[#718096] font-mono">{{ $s->destination->code }}</span>
                            </td>

                            <td class="px-4 py-4">
                                <span class="font-semibold text-[#172033]">{{ $s->carrier->name }}</span>
                                <span class="text-[10px] block text-[#718096]">{{ $s->carrier->code }} &bull; {{ $s->carrier->service_type }}</span>
                            </td>

                            <td class="px-4 py-4">
                                @if($s->is_delayed)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#F59E0B] bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                        <span>{{ __('ui.shipments.hours_late', ['hours' => $s->delay_hours]) }}</span>
                                        <span class="text-[9px] uppercase px-1 rounded bg-[#F59E0B] text-white font-bold">{{ __('ui.severity.' . ($s->delay_severity ?? 'NONE')) }}</span>
                                    </span>
                                    <div class="text-[10px] text-[#718096] mt-0.5">
                                        {{ __('ui.shipments.est_label') }} {{ $s->estimated_arrival ? $s->estimated_arrival->format('M d, H:i') : '-' }}
                                    </div>
                                @else
                                    <div class="text-[#10A37F] font-bold text-[11px]">
                                        {{ __('ui.status.ON_SCHEDULE') }}
                                    </div>
                                    <div class="text-[10px] text-[#718096]">
                                        ETA: {{ $s->expected_arrival->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @php
                                    $badge = match($s->status) {
                                        'CREATED' => 'bg-slate-100 text-[#718096] border-[#E6EAF0]',
                                        'BOOKED' => 'bg-amber-50 text-[#F59E0B] border-amber-200',
                                        'PICKED_UP' => 'bg-[#FFF7ED] text-[#F97316] border-[#FDBA74]/60',
                                        'IN_TRANSIT' => 'bg-blue-50 text-[#3B82F6] border-blue-200',
                                        'DELIVERED' => 'bg-emerald-50 text-[#10A37F] border-emerald-200',
                                        default => 'bg-red-50 text-[#F04452] border-red-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badge }}">
                                    {{ __('ui.status.' . $s->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('shipments.show', $s->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white font-bold transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>{{ __('ui.common.details') }}</span>
                                    </a>
                                    @if(Auth::user()->canManageShipments())
                                        <a href="{{ route('shipments.edit', $s->id) }}"
                                           class="p-1.5 rounded-lg bg-[#F4F5F9] text-[#718096] hover:text-[#172033] hover:bg-[#E6EAF0] transition" title="{{ __('ui.common.edit') }}">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <form method="POST" action="{{ route('shipments.destroy', $s->id) }}" class="inline" onsubmit="return confirm(@js(__('ui.shipments.confirm_delete')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-red-50 text-[#F04452] hover:bg-[#F04452] hover:text-white transition cursor-pointer" title="{{ __('ui.common.delete') }}">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-14 text-center">
                                <div class="inline-flex flex-col items-center gap-2 text-[#718096]">
                                    <i data-lucide="inbox" class="w-8 h-8 text-[#CBD5E1]"></i>
                                    <span class="text-xs font-medium">{{ __('ui.shipments.empty') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="p-4 border-t border-[#E6EAF0] bg-[#F4F5F9]/40">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
