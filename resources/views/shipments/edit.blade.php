@extends('layouts.app')

@section('title', 'Edit Shipment ' . $shipment->shipment_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Modify Shipment Parameters</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update vessel allocation, schedule, and consignment metrics</p>
        </div>
        <a href="{{ route('shipments.show', $shipment->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            &larr; Back to Details
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.update', $shipment->id) }}" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Carrier Line</label>
                <select name="carrier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    @foreach($carriers as $c)
                        <option value="{{ $c->id }}" {{ $shipment->carrier_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Cargo Category</label>
                <select name="cargo_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                    @foreach(config('cargotrace.cargo_types', []) as $key => $label)
                        <option value="{{ $key }}" {{ $shipment->cargo_type == $key ? 'selected' : '' }}>
                            {{ $key }} - {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Total Weight (KG)</label>
                <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', $shipment->total_weight_kg) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Total Volume (CBM)</label>
                <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', $shipment->total_volume_cbm) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Expected Departure (ETD)</label>
                <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', $shipment->expected_departure->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Expected Arrival (ETA)</label>
                <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Operational Notes</label>
            <textarea name="notes" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">{{ old('notes', $shipment->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-3">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">Save Changes</button>
        </div>
    </form>

</div>
@endsection
