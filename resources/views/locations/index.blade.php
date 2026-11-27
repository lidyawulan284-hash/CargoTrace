@extends('layouts.app')

@section('title', __('ui.locations.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.locations') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Photographic Port Hub Header Banner -->
    <div class="relative rounded-2xl overflow-hidden bg-[#172033] border border-[#E6EAF0] shadow-saas min-h-[145px] flex items-center">
        <img src="{{ asset('images/port-hub-crane.jpg') }}"
             onerror="this.src='{{ asset('images/roro-promo-card.png') }}'"
             alt="International Automotive Port & Terminal"
             class="absolute inset-0 w-full h-full object-cover object-center opacity-50">
        <div class="absolute inset-0 bg-gradient-to-r from-[#172033] via-[#172033]/90 to-[#172033]/35"></div>
        <div class="relative z-10 w-full p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ __('ui.locations.heading') }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">{{ __('ui.locations.subheading') }}</p>
            </div>
            @if(Auth::user()->isAdmin())
                <button type="button" onclick="document.getElementById('addLocModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer self-start sm:self-auto">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>{{ __('ui.locations.add_btn') }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.total_ports') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['total'] ?? $locations->total() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10A37F] flex items-center justify-center shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.active_ports') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center shrink-0">
                <i data-lucide="globe" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.countries_covered') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['countries'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#172033]/5 text-[#172033] flex items-center justify-center shrink-0">
                <i data-lucide="building-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.total_hubs') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['hubs'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Interactive Leaflet Network Map -->
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4 text-[#F97316]"></i>
                <h2 class="text-sm font-bold text-[#172033]">{{ __('ui.redesign.interactive_hub_map') }}</h2>
            </div>
            <span class="text-[11px] font-mono text-[#718096]">Leaflet.js &bull; OpenStreetMap</span>
        </div>
        <div id="hubsMapTileError" class="hidden mb-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 font-medium">
            {{ __('ui.shipments.map_tile_error') }}
        </div>
        <div id="hubsNetworkMap" class="w-full h-64 sm:h-72 rounded-xl border border-[#E6EAF0]"></div>
    </div>

    <!-- Search, Filter & View Switcher Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0] shadow-saas flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <form method="GET" action="{{ route('locations.index') }}" class="flex flex-wrap items-center gap-2.5 text-xs flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-[#718096] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.redesign.search_locations') }}"
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <select name="country" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.redesign.all_countries') }}</option>
                @foreach($countries ?? [] as $country)
                    <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.redesign.all_types') }}</option>
                @foreach(['PORT', 'AIRPORT', 'WAREHOUSE', 'CUSTOMS_FACILITY', 'HUB'] as $locType)
                    <option value="{{ $locType }}" {{ request('type') === $locType ? 'selected' : '' }}>{{ __('ui.location_types.' . $locType) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white rounded-xl font-bold transition cursor-pointer">{{ __('ui.common.filter') }}</button>
            <a href="{{ route('locations.index') }}" class="px-3 py-2 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] rounded-xl font-semibold transition">{{ __('ui.common.reset') }}</a>
        </form>

        <div class="inline-flex items-center rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] p-1 text-xs font-bold self-start lg:self-auto">
            <button type="button" id="btnLocTable" onclick="setLocationsView('table')" class="px-3 py-1.5 rounded-lg bg-[#172033] text-white transition cursor-pointer">{{ __('ui.redesign.view_table') }}</button>
            <button type="button" id="btnLocCards" onclick="setLocationsView('cards')" class="px-3 py-1.5 rounded-lg text-[#718096] hover:text-[#172033] transition cursor-pointer">{{ __('ui.redesign.view_cards') }}</button>
        </div>
    </div>

    <!-- Table View -->
    <div id="locationsTableView" class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F5F9] text-[#718096] uppercase text-[10px] font-bold tracking-wider border-b border-[#E6EAF0]">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('ui.locations.col_code') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.locations.col_name') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.locations.col_type') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.locations.col_city_country') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.locations.col_geo') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.locations.col_throughput') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.common.status') }}</th>
                        <th class="px-5 py-3.5 text-right">{{ __('ui.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6EAF0] font-medium text-[#172033]">
                    @forelse($locations as $l)
                        <tr class="hover:bg-[#F4F5F9]/60 transition">
                            <td class="px-5 py-3.5 font-mono font-extrabold text-[#F97316]">{{ $l->code }}</td>
                            <td class="px-4 py-3.5 font-bold text-[#172033]">{{ $l->name }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $l->type === 'AIRPORT' ? 'bg-blue-50 text-[#3B82F6]' : 'bg-[#F4F5F9] text-[#172033]' }}">
                                    {{ __('ui.location_types.' . $l->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">{{ $l->city }}, {{ $l->country }}</td>
                            <td class="px-4 py-3.5 font-mono text-[11px] text-[#718096]">
                                {{ number_format($l->latitude, 4) }}, {{ number_format($l->longitude, 4) }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-[#3B82F6]">{{ __('ui.locations.out_label', ['count' => $l->outbound_shipments_count]) }}</span>
                                <span class="text-[#CBD5E1] mx-1">/</span>
                                <span class="font-bold text-[#10A37F]">{{ __('ui.locations.in_label', ['count' => $l->inbound_shipments_count]) }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-[#10A37F]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#10A37F]"></span>
                                    {{ __('ui.redesign.operational_status') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <button type="button"
                                        onclick="openPortDetailModal(@js($l->code), @js($l->name), @js(__('ui.location_types.' . $l->type)), @js($l->city), @js($l->country), @js($l->address ?? __('ui.common.na')), @js(number_format($l->latitude, 5)), @js(number_format($l->longitude, 5)), {{ $l->outbound_shipments_count }}, {{ $l->inbound_shipments_count }})"
                                        class="px-2.5 py-1 rounded-lg bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white font-bold transition cursor-pointer">
                                    {{ __('ui.common.details') }}
                                </button>
                                @if(Auth::user()->isAdmin())
                                    <button type="button"
                                            onclick="openEditLocModal({{ $l->id }}, @js($l->code), @js($l->type), @js($l->name), @js($l->city), @js($l->country), @js($l->latitude), @js($l->longitude))"
                                            class="px-2.5 py-1 rounded-lg bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] font-bold transition cursor-pointer">
                                        {{ __('ui.common.edit') }}
                                    </button>
                                    <form method="POST" action="{{ route('locations.destroy', $l->id) }}" class="inline"
                                          onsubmit="return confirm(@js(__('ui.locations.confirm_delete', ['name' => $l->name])));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-[#F04452] text-[#F04452] hover:text-white font-bold transition cursor-pointer">
                                            {{ __('ui.common.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-[#718096]">{{ __('ui.locations.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="p-4 border-t border-[#E6EAF0] bg-[#F4F5F9]/40">
                {{ $locations->links() }}
            </div>
        @endif
    </div>

    <!-- Card View -->
    <div id="locationsCardView" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($locations as $l)
            <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-5 flex flex-col justify-between space-y-3 hover:border-[#F97316]/50 transition">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="font-mono text-xs font-extrabold text-[#F97316]">{{ $l->code }}</span>
                        <h3 class="text-sm font-extrabold text-[#172033] mt-0.5">{{ $l->name }}</h3>
                        <p class="text-xs text-[#718096] mt-0.5">{{ $l->city }}, {{ $l->country }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#F4F5F9] text-[#172033]">
                        {{ __('ui.location_types.' . $l->type) }}
                    </span>
                </div>
                <div class="pt-3 border-t border-[#E6EAF0] flex items-center justify-between text-xs">
                    <span class="font-mono text-[11px] text-[#718096]">{{ number_format($l->latitude, 4) }}, {{ number_format($l->longitude, 4) }}</span>
                    <span class="font-bold text-[#10A37F]">{{ $l->outbound_shipments_count + $l->inbound_shipments_count }} {{ __('ui.common.units_lower') }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#E6EAF0] py-12 text-center text-xs text-[#718096]">{{ __('ui.locations.empty') }}</div>
        @endforelse
    </div>

</div>

<!-- Port Details Modal -->
<div id="portDetailModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]" id="portDetailName">{{ __('ui.redesign.port_details') }}</h3>
            <button type="button" onclick="document.getElementById('portDetailModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]"><span class="text-[#718096]">UN/LOCODE</span><strong class="font-mono text-[#F97316]" id="portDetailCode"></strong></div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.locations.col_type') }}</span><strong class="text-[#172033]" id="portDetailType"></strong></div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.locations.col_city_country') }}</span><strong class="text-[#172033]" id="portDetailLocation"></strong></div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.locations.col_geo') }}</span><strong class="font-mono text-[#172033]" id="portDetailGeo"></strong></div>
            <div class="flex justify-between py-1.5"><span class="text-[#718096]">{{ __('ui.redesign.capacity_label') }}</span><strong class="text-[#10A37F]" id="portDetailThroughput"></strong></div>
        </div>
        <div class="flex justify-end pt-2">
            <button type="button" onclick="document.getElementById('portDetailModal').classList.add('hidden')" class="px-4 py-2 bg-[#172033] text-white font-bold rounded-xl cursor-pointer">{{ __('ui.common.close') }}</button>
        </div>
    </div>
</div>

<!-- Admin Add Location Modal -->
@if(Auth::user()->isAdmin())
<div id="addLocModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.locations.modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('addLocModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('locations.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_code') }}</label>
                    <input type="text" name="code" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] uppercase font-mono" placeholder="e.g. IDTPP">
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_type') }}</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                        @foreach(['PORT', 'AIRPORT', 'WAREHOUSE', 'CUSTOMS_FACILITY', 'HUB'] as $locType)
                            <option value="{{ $locType }}">{{ __('ui.location_types.' . $locType) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_name') }}</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="e.g. Jakarta / Tanjung Priok Car Terminal">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_city') }}</label>
                    <input type="text" id="addLocCity" name="city" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="Jakarta">
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_country') }}</label>
                    <input type="text" id="addLocCountry" name="country" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="Indonesia">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_lat') }}</label>
                    <input type="number" step="0.0000001" id="addLocLat" name="latitude" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="-6.0967">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-[#172033]">{{ __('ui.locations.field_lng') }}</label>
                        <button type="button" id="addLocGeocodeBtn" onclick="geocodeLocation('addLocCity', 'addLocCountry', 'addLocLat', 'addLocLng', 'addLocGeocodeBtn', 'addLocGeocodeMsg')" class="text-[10px] font-bold text-[#F97316] hover:text-[#EA580C] underline cursor-pointer">
                            {{ __('ui.shipments.lookup_coords_btn') }}
                        </button>
                    </div>
                    <input type="number" step="0.0000001" id="addLocLng" name="longitude" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="106.8858">
                </div>
            </div>
            <p id="addLocGeocodeMsg" class="hidden text-[10px] text-[#F59E0B] font-medium"></p>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addLocModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.locations.save_btn') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Admin Edit Location Modal -->
<div id="editLocModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.locations.edit_modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('editLocModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editLocForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_code') }}</label>
                    <input type="text" id="editLocCode" name="code" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] uppercase font-mono">
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_type') }}</label>
                    <select id="editLocType" name="type" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                        @foreach(['PORT', 'AIRPORT', 'WAREHOUSE', 'CUSTOMS_FACILITY', 'HUB'] as $locType)
                            <option value="{{ $locType }}">{{ __('ui.location_types.' . $locType) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_name') }}</label>
                <input type="text" id="editLocName" name="name" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_city') }}</label>
                    <input type="text" id="editLocCity" name="city" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_country') }}</label>
                    <input type="text" id="editLocCountry" name="country" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.locations.field_lat') }}</label>
                    <input type="number" step="0.0000001" id="editLocLat" name="latitude" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-[#172033]">{{ __('ui.locations.field_lng') }}</label>
                        <button type="button" id="editLocGeocodeBtn" onclick="geocodeLocation('editLocCity', 'editLocCountry', 'editLocLat', 'editLocLng', 'editLocGeocodeBtn', 'editLocGeocodeMsg')" class="text-[10px] font-bold text-[#F97316] hover:text-[#EA580C] underline cursor-pointer">
                            {{ __('ui.shipments.lookup_coords_btn') }}
                        </button>
                    </div>
                    <input type="number" step="0.0000001" id="editLocLng" name="longitude" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                </div>
            </div>
            <p id="editLocGeocodeMsg" class="hidden text-[10px] text-[#F59E0B] font-medium"></p>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editLocModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.common.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const LOC_GEOCODE_URL = @json(route('geocode.lookup'));
const LOC_GEOCODE_I18N = {
    btn: @json(__('ui.shipments.lookup_coords_btn')),
    loading: @json(__('ui.shipments.lookup_coords_loading')),
    failed: @json(__('ui.shipments.lookup_coords_failed')),
};
const HUB_LOCATIONS = @json($mapLocations ?? []);

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function setLocationsView(mode) {
    const tableEl = document.getElementById('locationsTableView');
    const cardEl = document.getElementById('locationsCardView');
    const btnTable = document.getElementById('btnLocTable');
    const btnCards = document.getElementById('btnLocCards');

    if (mode === 'cards') {
        tableEl?.classList.add('hidden');
        cardEl?.classList.remove('hidden');
        btnCards?.classList.add('bg-[#172033]', 'text-white');
        btnCards?.classList.remove('text-[#718096]');
        btnTable?.classList.remove('bg-[#172033]', 'text-white');
        btnTable?.classList.add('text-[#718096]');
    } else {
        cardEl?.classList.add('hidden');
        tableEl?.classList.remove('hidden');
        btnTable?.classList.add('bg-[#172033]', 'text-white');
        btnTable?.classList.remove('text-[#718096]');
        btnCards?.classList.remove('bg-[#172033]', 'text-white');
        btnCards?.classList.add('text-[#718096]');
    }
}

function openPortDetailModal(code, name, type, city, country, address, lat, lng, outCount, inCount) {
    document.getElementById('portDetailName').innerText = name;
    document.getElementById('portDetailCode').innerText = code;
    document.getElementById('portDetailType').innerText = type;
    document.getElementById('portDetailLocation').innerText = `${city}, ${country}`;
    document.getElementById('portDetailGeo').innerText = `${lat}, ${lng}`;
    document.getElementById('portDetailThroughput').innerText = `${outCount} out / ${inCount} in`;
    document.getElementById('portDetailModal').classList.remove('hidden');
}

function openEditLocModal(id, code, type, name, city, country, lat, lng) {
    document.getElementById('editLocForm').action = '/locations/' + id;
    document.getElementById('editLocCode').value = code;
    document.getElementById('editLocType').value = type;
    document.getElementById('editLocName').value = name;
    document.getElementById('editLocCity').value = city;
    document.getElementById('editLocCountry').value = country;
    document.getElementById('editLocLat').value = lat;
    document.getElementById('editLocLng').value = lng;
    document.getElementById('editLocGeocodeMsg')?.classList.add('hidden');
    document.getElementById('editLocModal').classList.remove('hidden');
}

async function geocodeLocation(cityId, countryId, latId, lngId, btnId, msgId) {
    const city = (document.getElementById(cityId)?.value || '').trim();
    const country = (document.getElementById(countryId)?.value || '').trim();
    const query = [city, country].filter(Boolean).join(', ');
    const btn = document.getElementById(btnId);
    const msg = document.getElementById(msgId);
    if (query.length < 2 || !btn) return;

    btn.disabled = true;
    btn.innerText = LOC_GEOCODE_I18N.loading;
    msg?.classList.add('hidden');

    try {
        const res = await fetch(`${LOC_GEOCODE_URL}?q=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (json.success && json.data && json.data.latitude !== null && json.data.longitude !== null) {
            document.getElementById(latId).value = json.data.latitude;
            document.getElementById(lngId).value = json.data.longitude;
        } else if (msg) {
            msg.innerText = json.data?.message || LOC_GEOCODE_I18N.failed;
            msg.classList.remove('hidden');
        }
    } catch (err) {
        if (msg) {
            msg.innerText = LOC_GEOCODE_I18N.failed;
            msg.classList.remove('hidden');
        }
    } finally {
        btn.disabled = false;
        btn.innerText = LOC_GEOCODE_I18N.btn;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('hubsNetworkMap');
    if (!mapEl || typeof L === 'undefined') return;

    const map = L.map('hubsNetworkMap', { scrollWheelZoom: false }).setView([22, 65], 2);
    const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
    }).addTo(map);

    const errBanner = document.getElementById('hubsMapTileError');
    tiles.on('tileerror', () => errBanner?.classList.remove('hidden'));
    tiles.on('load', () => errBanner?.classList.add('hidden'));

    const bounds = [];
    HUB_LOCATIONS.forEach(loc => {
        if (!loc.lat && !loc.lng) return;
        bounds.push([loc.lat, loc.lng]);
        const color = loc.type === 'AIRPORT' ? '#3B82F6' : '#F97316';
        const icon = L.divIcon({
            className: 'hub-pin',
            html: `<div style="background:${color};width:24px;height:24px;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(23,32,51,0.35);display:flex;align-items:center;justify-content:center;color:#fff;font-size:9px;font-weight:800;">${escapeHtml(loc.code.slice(0, 2))}</div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        L.marker([loc.lat, loc.lng], { icon }).addTo(map).bindPopup(`
            <div style="font-size:12px;min-width:170px;">
                <strong style="color:#172033;">${escapeHtml(loc.name)}</strong><br/>
                <span style="color:#F97316;font-weight:700;font-family:monospace;">${escapeHtml(loc.code)}</span> &bull; ${escapeHtml(loc.type)}<br/>
                <span style="color:#718096;">${escapeHtml(loc.city)}, ${escapeHtml(loc.country)}</span><br/>
                <span style="color:#10A37F;font-weight:600;">${loc.outbound} out / ${loc.inbound} in</span>
            </div>
        `);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [35, 35], maxZoom: 5 });
    }
});
</script>
@endpush
