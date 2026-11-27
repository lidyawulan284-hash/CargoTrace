@extends('layouts.app')

@section('title', __('ui.shipments.create_title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <a href="{{ route('shipments.index') }}" class="hover:text-[#F97316] transition font-medium">{{ __('ui.nav.vehicle_shipments') }}</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.shipments.new_booking') }}</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.shipments.create_heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.shipments.create_subheading') }}</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-[#718096] hover:text-[#F97316] flex items-center gap-1 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>{{ __('ui.shipments.back_to_registry') }}</span>
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Consignment & Carrier Specification -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6EAF0] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#172033] uppercase tracking-wider border-b border-[#E6EAF0] pb-2">
                {{ __('ui.shipments.section_1') }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.tracking_ref') }}</label>
                    <input type="text" name="shipment_number" value="{{ old('shipment_number', $suggestedNumber) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] font-mono text-sm uppercase focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                    <span class="text-[10px] text-[#718096]">{{ __('ui.shipments.auto_generated_hint') }}</span>
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.consignee_label') }}</label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                        <option value="">{{ __('ui.shipments.select_customer') }}</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} {{ $cust->company_name ? "({$cust->company_name})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.carrier_line_label') }}</label>
                    <select name="carrier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                        <option value="">{{ __('ui.shipments.select_carrier') }}</option>
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }} - {{ $c->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.primary_cargo_type') }}</label>
                    <select name="cargo_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                        @foreach(config('cargotrace.cargo_types', []) as $key => $label)
                            <option value="{{ $key }}" {{ old('cargo_type') == $key ? 'selected' : '' }}>
                                {{ $key }} - {{ __('ui.cargo_types.' . $key) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs pt-2">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.gross_weight') }}</label>
                    <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', 2100) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.total_volume') }}</label>
                    <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', 14.5) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                </div>
            </div>
        </div>

        <!-- 2. Transit Route & Schedule Card -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6EAF0] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-[#172033] uppercase tracking-wider border-b border-[#E6EAF0] pb-2">
                {{ __('ui.shipments.section_2') }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.origin_port_label') }}</label>
                    <select name="origin_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                        <option value="">{{ __('ui.shipments.select_origin') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->code }} &bull; {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.dest_port_label') }}</label>
                    <select name="destination_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] focus:outline-none">
                        <option value="">{{ __('ui.shipments.select_destination') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->code }} &bull; {{ $loc->name }} ({{ $loc->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.scheduled_departure') }}</label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                </div>

                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.scheduled_arrival') }}</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#172033] mb-1">{{ __('ui.shipments.handling_notes') }}</label>
                <textarea name="notes" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-[#E6EAF0] text-xs" placeholder="{{ __('ui.shipments.handling_notes_placeholder') }}"></textarea>
            </div>
        </div>

        <!-- 3. Vehicle Items Manifest -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6EAF0] shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-2">
                <h2 class="text-sm font-bold text-[#172033] uppercase tracking-wider">
                    {{ __('ui.shipments.section_3') }}
                </h2>
                <button type="button" id="addItemBtn" class="text-xs font-bold text-[#F97316] hover:text-[#EA580C] flex items-center gap-1">
                    <span>{{ __('ui.shipments.add_vehicle_unit') }}</span>
                </button>
            </div>

            <div id="itemsContainer" class="space-y-3">
                <div class="item-row grid grid-cols-12 gap-2 text-xs items-center bg-[#F4F5F9] p-3 rounded-xl border border-[#E6EAF0]">
                    <div class="col-span-12 sm:col-span-3">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.shipments.vehicle_model') }}</label>
                        <input type="text" name="items[0][vehicle_model]" value="Hyundai Ioniq 5 CBU" required
                               class="w-full px-2.5 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.shipments.vin_unique') }}</label>
                        <input type="text" name="items[0][vin_number]" placeholder="e.g. KMHKN81EFPU000101"
                               class="w-full px-2.5 py-1.5 rounded-lg border border-[#E6EAF0] bg-white font-mono uppercase">
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.shipments.category') }}</label>
                        <select name="items[0][cargo_category]" class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                            @foreach(\App\Models\ShipmentItem::CATEGORIES as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-4 sm:col-span-1">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.common.qty') }}</label>
                        <input type="number" name="items[0][quantity]" value="1" min="1" required
                               class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                    </div>
                    <div class="col-span-4 sm:col-span-1">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.shipments.weight_kg') }}</label>
                        <input type="number" step="0.1" name="items[0][weight_kg]" value="2100" required
                               class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <label class="block font-semibold text-[#718096] mb-0.5">{{ __('ui.shipments.declared_value') }}</label>
                        <input type="number" step="0.01" name="items[0][declared_value]" value="54000" min="0"
                               class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                {{ __('ui.common.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                {{ __('ui.shipments.confirm_create') }}
            </button>
        </div>

    </form>

</div>

@php
    $createI18n = [
        'vehicleModel' => __('ui.shipments.vehicle_model'),
        'vinNumber' => __('ui.shipments.vin_number'),
        'vinPlaceholder' => __('ui.shipments.vin_placeholder'),
        'category' => __('ui.shipments.category'),
        'qty' => __('ui.common.qty'),
        'weightShort' => __('ui.shipments.weight_short'),
        'valueShort' => __('ui.shipments.value_short'),
    ];
@endphp
<script>
const CREATE_I18N = @json($createI18n);

let itemIndex = 1;
document.getElementById('addItemBtn').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-2 text-xs items-center bg-[#F4F5F9] p-3 rounded-xl border border-[#E6EAF0]';
    row.innerHTML = `
        <div class="col-span-12 sm:col-span-3">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.vehicleModel}</label>
            <input type="text" name="items[${itemIndex}][vehicle_model]" required placeholder="e.g. Ducati Panigale V4"
                   class="w-full px-2.5 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
        </div>
        <div class="col-span-6 sm:col-span-3">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.vinNumber}</label>
            <input type="text" name="items[${itemIndex}][vin_number]" placeholder="${CREATE_I18N.vinPlaceholder}"
                   class="w-full px-2.5 py-1.5 rounded-lg border border-[#E6EAF0] bg-white font-mono uppercase">
        </div>
        <div class="col-span-6 sm:col-span-2">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.category}</label>
            <select name="items[${itemIndex}][cargo_category]" class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
                <option value="CBU_EV">CBU_EV</option>
                <option value="MOTORSPORT_AIR">MOTORSPORT_AIR</option>
                <option value="CKD_PARTS">CKD_PARTS</option>
                <option value="LUXURY_CAR">LUXURY_CAR</option>
            </select>
        </div>
        <div class="col-span-4 sm:col-span-1">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.qty}</label>
            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required
                   class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
        </div>
        <div class="col-span-4 sm:col-span-1">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.weightShort}</label>
            <input type="number" step="0.1" name="items[${itemIndex}][weight_kg]" value="200" required
                   class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
        </div>
        <div class="col-span-3 sm:col-span-1">
            <label class="block font-semibold text-[#718096] mb-0.5">${CREATE_I18N.valueShort}</label>
            <input type="number" step="0.01" name="items[${itemIndex}][declared_value]" value="35000" min="0"
                   class="w-full px-2 py-1.5 rounded-lg border border-[#E6EAF0] bg-white">
        </div>
        <div class="col-span-1 text-center pt-4">
            <button type="button" onclick="this.closest('.item-row').remove()" class="text-rose-500 hover:text-rose-700 font-bold text-base">&times;</button>
        </div>
    `;
    container.appendChild(row);
    itemIndex++;
});
</script>
@endsection
