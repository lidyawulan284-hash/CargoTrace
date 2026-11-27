@extends('layouts.app')

@section('title', __('ui.shipments.edit_title', ['number' => $shipment->shipment_number]))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <a href="{{ route('shipments.index') }}" class="hover:text-[#F97316] transition font-medium">{{ __('ui.nav.vehicle_shipments') }}</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <a href="{{ route('shipments.show', $shipment->id) }}" class="hover:text-[#F97316] transition font-mono font-medium">{{ $shipment->shipment_number }}</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.common.edit') }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.shipments.edit_heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.shipments.edit_subheading') }}</p>
        </div>
        <a href="{{ route('shipments.show', $shipment->id) }}" class="text-xs font-bold text-[#718096] hover:text-[#F97316] flex items-center gap-1 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>{{ __('ui.shipments.back_to_details') }}</span>
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.update', $shipment->id) }}" class="bg-white p-6 rounded-2xl border border-[#E6EAF0] shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.col_carrier') }}</label>
                <select name="carrier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $shipment->carrier_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.cargo_category') }}</label>
                <select name="cargo_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
                    @foreach(config('cargotrace.cargo_types', []) as $key => $label)
                        <option value="{{ $key }}" {{ $shipment->cargo_type == $key ? 'selected' : '' }}>
                            {{ $key }} - {{ __('ui.cargo_types.' . $key) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.total_weight') }}</label>
                <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', $shipment->total_weight_kg) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.total_volume') }}</label>
                <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', $shipment->total_volume_cbm) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.expected_departure') }}</label>
                <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', $shipment->expected_departure->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
            </div>

            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.shipments.expected_arrival') }}</label>
                <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6EAF0] text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#172033] mb-1">{{ __('ui.shipments.operational_notes') }}</label>
            <textarea name="notes" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-[#E6EAF0] text-xs">{{ old('notes', $shipment->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-3">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-4 py-2 border rounded-xl text-xs font-bold text-[#718096]">{{ __('ui.common.cancel') }}</a>
            <button type="submit" class="px-6 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl text-xs font-bold transition cursor-pointer">{{ __('ui.common.save_changes') }}</button>
        </div>
    </form>

</div>
@endsection
