@extends('layouts.app')

@section('title', 'Register New Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">New Consignment Booking</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register cargo details, carrier allocation, and voyage schedule</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            &larr; Back to Registry
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
        @csrf

        <!-- General Booking Info Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Consignment & Carrier Specification
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Shipment Tracking Ref #</label>
                    <input type="text" name="shipment_number" value="{{ old('shipment_number', $suggestedNumber) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-sm uppercase focus:ring-2 focus:ring-sky-500">
                    <span class="text-[10px] text-slate-400">System auto-generated identifier</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Consignee / Customer Account</label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Customer...</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} {{ $cust->company_name ? "({$cust->company_name})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Carrier Line / Forwarder</label>
                    <select name="carrier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Carrier...</option>
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }} - {{ $c->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Cargo Classification</label>
                    <select name="cargo_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                        @foreach(config('cargotrace.cargo_types', []) as $key => $label)
                            <option value="{{ $key }}" {{ old('cargo_type') == $key ? 'selected' : '' }}>
                                {{ $key }} - {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs pt-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Total Gross Weight (KG)</label>
                    <input type="number" step="0.01" name="total_weight_kg" value="{{ old('total_weight_kg', 1250) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Total Volume (CBM)</label>
                    <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', 8.5) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>
        </div>

        <!-- Transit Route & Schedule Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                2. Origin, Destination & Voyage Timeline
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Origin Port / Logistics Center</label>
                    <select name="origin_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Origin...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Destination Port / Hub</label>
                    <select name="destination_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Destination...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Scheduled Departure (ETD)</label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Scheduled Arrival (ETA Baseline)</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Manifest Notes & Handling Instructions</label>
                <textarea name="notes" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. Temperature sensitive cold chain, customs pre-cleared..."></textarea>
            </div>
        </div>

        <!-- Cargo Items Breakdown (Slide 11) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    3. Cargo Items Manifest (`shipment_items`)
                </h2>
                <button type="button" id="addItemBtn" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                    <span>+ Add Cargo Item</span>
                </button>
            </div>

            <div id="itemsContainer" class="space-y-3">
                <div class="item-row grid grid-cols-12 gap-2 text-xs items-center bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                    <div class="col-span-5">
                        <label class="block font-semibold text-slate-500 mb-0.5">Description</label>
                        <input type="text" name="items[0][item_description]" value="Electronic Microcontrollers" required
                               class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-500 mb-0.5">Qty (Units)</label>
                        <input type="number" name="items[0][quantity]" value="500" min="1" required
                               class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-500 mb-0.5">Weight (KG)</label>
                        <input type="number" step="0.1" name="items[0][weight_kg]" value="620" required
                               class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-500 mb-0.5">HS Tariff Code</label>
                        <input type="text" name="items[0][hs_code]" value="8542.31.00"
                               class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white font-mono">
                    </div>
                    <div class="col-span-1 text-center pt-4">
                        <span class="text-slate-300">&bull;</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-lg transition">
                Confirm & Create Shipment Booking
            </button>
        </div>

    </form>

</div>

<script>
let itemIndex = 1;
document.getElementById('addItemBtn').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-2 text-xs items-center bg-slate-50 p-3 rounded-xl border border-slate-200/70';
    row.innerHTML = `
        <div class="col-span-5">
            <label class="block font-semibold text-slate-500 mb-0.5">Description</label>
            <input type="text" name="items[${itemIndex}][item_description]" required placeholder="Item description"
                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
        </div>
        <div class="col-span-2">
            <label class="block font-semibold text-slate-500 mb-0.5">Qty</label>
            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required
                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
        </div>
        <div class="col-span-2">
            <label class="block font-semibold text-slate-500 mb-0.5">Weight (KG)</label>
            <input type="number" step="0.1" name="items[${itemIndex}][weight_kg]" value="10" required
                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
        </div>
        <div class="col-span-2">
            <label class="block font-semibold text-slate-500 mb-0.5">HS Tariff Code</label>
            <input type="text" name="items[${itemIndex}][hs_code]" placeholder="e.g. 8471.30"
                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white font-mono">
        </div>
        <div class="col-span-1 text-center pt-4">
            <button type="button" onclick="this.closest('.item-row').remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
        </div>
    `;
    container.appendChild(row);
    itemIndex++;
});
</script>
@endsection
