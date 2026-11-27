@extends('layouts.app')

@section('title', __('ui.carriers.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.carriers') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.carriers.heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.carriers.subheading') }}</p>
        </div>
        @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('addCarrierModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer self-start sm:self-auto">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>{{ __('ui.carriers.add_btn') }}</span>
            </button>
        @endif
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                <i data-lucide="anchor" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.total_carriers') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['total'] ?? $carriers->total() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10A37F] flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.active_carriers') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center shrink-0">
                <i data-lucide="ship" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.roro_carriers') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['roro'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#172033]/5 text-[#172033] flex items-center justify-center shrink-0">
                <i data-lucide="plane" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.air_carriers') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['air'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0] shadow-saas">
        <form method="GET" action="{{ route('carriers.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-[#718096] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.redesign.search_carriers') }}"
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <select name="service_type" class="px-3.5 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.redesign.all_modes') }}</option>
                @foreach(config('cargotrace.carrier_service_types', []) as $modeKey => $modeLabel)
                    <option value="{{ $modeKey }}" {{ request('service_type') === $modeKey ? 'selected' : '' }}>{{ __('ui.carrier_modes.' . $modeKey) }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3.5 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.redesign.all_statuses') }}</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('ui.common.active') }}</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('ui.common.inactive') }}</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white rounded-xl font-bold transition cursor-pointer">{{ __('ui.common.filter') }}</button>
            <a href="{{ route('carriers.index') }}" class="px-3.5 py-2 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] rounded-xl font-semibold transition">{{ __('ui.common.reset') }}</a>
        </form>
    </div>

    <!-- Carriers Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($carriers as $c)
            @php
                $isAir = $c->service_type === 'AIR_FREIGHT';
                $routes = $c->shipments
                    ->map(fn ($s) => ($s->origin?->city && $s->destination?->city) ? "{$s->origin->city} → {$s->destination->city}" : null)
                    ->filter()
                    ->unique()
                    ->take(3)
                    ->values();
            @endphp
            <div class="bg-white p-5 rounded-2xl border border-[#E6EAF0] shadow-saas hover:border-[#F97316]/50 transition flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl {{ $isAir ? 'bg-[#172033] text-[#FDBA74]' : 'bg-[#FFF7ED] text-[#F97316] border border-[#FDBA74]/50' }} flex items-center justify-center font-mono font-extrabold text-sm shrink-0">
                                {{ strtoupper(substr($c->code, 0, 3)) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-[#172033] leading-snug">{{ $c->name }}</h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[10px] font-mono font-bold text-[#F97316]">{{ $c->code }}</span>
                                    <span class="text-[#CBD5E1]">&bull;</span>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $isAir ? 'bg-blue-50 text-[#3B82F6]' : 'bg-[#F4F5F9] text-[#172033]' }}">
                                        {{ __('ui.carrier_modes.' . $c->service_type) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-emerald-50 text-[#10A37F]' : 'bg-slate-100 text-[#718096]' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $c->is_active ? 'bg-[#10A37F]' : 'bg-[#718096]' }}"></span>
                            {{ $c->is_active ? __('ui.common.active') : __('ui.common.inactive') }}
                        </span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#E6EAF0] space-y-1.5 text-xs text-[#718096]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-[#718096] shrink-0"></i>
                            <span class="truncate text-[#172033] font-medium">{{ $c->contact_email ?? __('ui.common.na') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-[#718096] shrink-0"></i>
                            <span class="text-[#172033] font-medium">{{ $c->contact_phone ?? __('ui.common.na') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="container" class="w-3.5 h-3.5 text-[#F97316] shrink-0"></i>
                            <span>{{ __('ui.carriers.total_shipments') }} <strong class="text-[#172033]">{{ __('ui.carriers.voyages', ['count' => $c->shipments_count]) }}</strong></span>
                        </div>
                    </div>

                    <!-- Operating Routes -->
                    <div class="mt-3 pt-3 border-t border-[#E6EAF0]">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[#718096] mb-1.5">{{ __('ui.redesign.operating_routes') }}</div>
                        @if($routes->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($routes as $routeLabel)
                                    <span class="px-2 py-0.5 rounded-lg bg-[#F4F5F9] border border-[#E6EAF0] text-[10px] font-semibold text-[#172033]">{{ $routeLabel }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-[11px] text-[#718096] italic">{{ __('ui.redesign.no_routes_yet') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-3 border-t border-[#E6EAF0] flex items-center justify-between gap-2 text-xs">
                    <button type="button"
                            onclick="openCarrierDetailModal(@js($c->name), @js($c->code), @js(__('ui.carrier_modes.' . $c->service_type)), @js($c->contact_email ?? __('ui.common.na')), @js($c->contact_phone ?? __('ui.common.na')), @js($c->website ?? ''), {{ $c->shipments_count }}, @js($routes->all()))"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white font-bold transition cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>{{ __('ui.common.details') }}</span>
                    </button>

                    @if(Auth::user()->isAdmin())
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    onclick="openEditCarrierModal({{ $c->id }}, @js($c->code), @js($c->name), @js($c->service_type), @js($c->contact_email ?? ''), @js($c->contact_phone ?? ''), @js((bool) $c->is_active))"
                                    class="px-2.5 py-1.5 rounded-lg bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] font-bold transition cursor-pointer">
                                {{ __('ui.common.edit') }}
                            </button>
                            <form method="POST" action="{{ route('carriers.destroy', $c->id) }}" class="inline"
                                  onsubmit="return confirm(@js(__('ui.carriers.confirm_delete', ['name' => $c->name])));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-[#F04452] text-[#F04452] hover:text-white font-bold transition cursor-pointer">
                                    {{ __('ui.common.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#E6EAF0] py-14 text-center text-xs text-[#718096]">
                {{ __('ui.carriers.empty') }}
            </div>
        @endforelse
    </div>

    @if($carriers->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0]">
            {{ $carriers->links() }}
        </div>
    @endif

</div>

<!-- Carrier Details Modal -->
<div id="carrierDetailModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]" id="detailCarrierTitle">{{ __('ui.redesign.carrier_details') }}</h3>
            <button type="button" onclick="document.getElementById('carrierDetailModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-2.5">
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]">
                <span class="text-[#718096]">{{ __('ui.carriers.code_label') }}</span>
                <strong class="font-mono text-[#F97316]" id="detailCarrierCode"></strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]">
                <span class="text-[#718096]">{{ __('ui.carriers.field_mode') }}</span>
                <strong class="text-[#172033]" id="detailCarrierMode"></strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]">
                <span class="text-[#718096]">{{ __('ui.carriers.email_label') }}</span>
                <strong class="text-[#172033]" id="detailCarrierEmail"></strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]">
                <span class="text-[#718096]">{{ __('ui.carriers.phone_label') }}</span>
                <strong class="text-[#172033]" id="detailCarrierPhone"></strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#E6EAF0]">
                <span class="text-[#718096]">{{ __('ui.carriers.total_shipments') }}</span>
                <strong class="text-[#172033]" id="detailCarrierCount"></strong>
            </div>
            <div>
                <span class="text-[#718096] block mb-1.5">{{ __('ui.redesign.operating_routes') }}</span>
                <div id="detailCarrierRoutes" class="flex flex-wrap gap-1.5"></div>
            </div>
        </div>
        <div class="flex justify-end pt-2">
            <button type="button" onclick="document.getElementById('carrierDetailModal').classList.add('hidden')" class="px-4 py-2 bg-[#172033] text-white font-bold rounded-xl cursor-pointer">{{ __('ui.common.close') }}</button>
        </div>
    </div>
</div>

<!-- Admin Add Carrier Modal -->
@if(Auth::user()->isAdmin())
<div id="addCarrierModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.carriers.modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('carriers.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_code') }}</label>
                <input type="text" name="code" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none uppercase font-mono">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_name') }}</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none" placeholder="e.g. Wallenius Wilhelmsen">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_mode') }}</label>
                <select name="service_type" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                    @foreach(config('cargotrace.carrier_service_types', []) as $modeKey => $modeLabel)
                        <option value="{{ $modeKey }}">{{ $modeKey }} &mdash; {{ __('ui.carrier_modes.' . $modeKey) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_email') }}</label>
                <input type="email" name="contact_email" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none" placeholder="ops@carrier.com">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_phone') }}</label>
                <input type="text" name="contact_phone" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none" placeholder="+62 ...">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.carriers.save_btn') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Admin Edit Carrier Modal -->
<div id="editCarrierModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.carriers.edit_modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editCarrierForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_code') }}</label>
                <input type="text" id="editCarrierCode" name="code" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] uppercase font-mono">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_name') }}</label>
                <input type="text" id="editCarrierName" name="name" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_mode') }}</label>
                <select id="editCarrierMode" name="service_type" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                    @foreach(config('cargotrace.carrier_service_types', []) as $modeKey => $modeLabel)
                        <option value="{{ $modeKey }}">{{ $modeKey }} &mdash; {{ __('ui.carrier_modes.' . $modeKey) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_email') }}</label>
                <input type="email" id="editCarrierEmail" name="contact_email" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.carriers.field_phone') }}</label>
                <input type="text" id="editCarrierPhone" name="contact_phone" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <label class="flex items-center gap-2 text-[#172033] font-semibold cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="editCarrierActive" name="is_active" value="1" class="rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                <span>{{ __('ui.carriers.status_active') }}</span>
            </label>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.common.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openCarrierDetailModal(name, code, mode, email, phone, website, count, routes) {
    document.getElementById('detailCarrierTitle').innerText = name;
    document.getElementById('detailCarrierCode').innerText = code;
    document.getElementById('detailCarrierMode').innerText = mode;
    document.getElementById('detailCarrierEmail').innerText = email;
    document.getElementById('detailCarrierPhone').innerText = phone;
    document.getElementById('detailCarrierCount').innerText = count;

    const routesEl = document.getElementById('detailCarrierRoutes');
    routesEl.innerHTML = '';
    if (Array.isArray(routes) && routes.length > 0) {
        routes.forEach(r => {
            const chip = document.createElement('span');
            chip.className = 'px-2.5 py-1 rounded-lg bg-[#FFF7ED] border border-[#FDBA74]/60 text-[11px] font-semibold text-[#172033]';
            chip.textContent = r;
            routesEl.appendChild(chip);
        });
    } else {
        const empty = document.createElement('span');
        empty.className = 'text-[#718096] italic';
        empty.textContent = @json(__('ui.redesign.no_routes_yet'));
        routesEl.appendChild(empty);
    }

    document.getElementById('carrierDetailModal').classList.remove('hidden');
}

function openEditCarrierModal(id, code, name, mode, email, phone, isActive) {
    document.getElementById('editCarrierForm').action = '/carriers/' + id;
    document.getElementById('editCarrierCode').value = code;
    document.getElementById('editCarrierName').value = name;
    document.getElementById('editCarrierMode').value = mode;
    document.getElementById('editCarrierEmail').value = email;
    document.getElementById('editCarrierPhone').value = phone;
    document.getElementById('editCarrierActive').checked = Boolean(isActive);
    document.getElementById('editCarrierModal').classList.remove('hidden');
}
</script>
@endpush
