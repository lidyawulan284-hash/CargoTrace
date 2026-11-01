@extends('layouts.app')

@section('title', 'Locations & Ports Directory')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Global Logistics Nodes & Ports</h1>
            <p class="text-xs text-slate-500 mt-0.5">Maritime container terminals, international air hubs, and transit depots</p>
        </div>
        @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('addLocModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                + Add Logistics Node
            </button>
        @endif
    </div>

    <!-- Locations Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Node Code</th>
                        <th class="px-4 py-3.5">Facility Name</th>
                        <th class="px-4 py-3.5">Type</th>
                        <th class="px-4 py-3.5">City & Country</th>
                        <th class="px-4 py-3.5">Geographic Fix</th>
                        <th class="px-4 py-3.5 text-right">Throughput</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                    @forelse($locations as $l)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">{{ $l->code }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-800">{{ $l->name }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $l->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">{{ $l->city }}, {{ $l->country }}</td>
                            <td class="px-4 py-3.5 font-mono text-slate-500">
                                {{ number_format($l->latitude, 4) }}, {{ number_format($l->longitude, 4) }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="font-bold text-sky-600">{{ $l->outbound_shipments_count }} out</span> / 
                                <span class="font-bold text-emerald-600">{{ $l->inbound_shipments_count }} in</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No locations registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $locations->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Admin Add Location Modal -->
@if(Auth::user()->isAdmin())
<div id="addLocModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Add Logistics Node / Port</h3>
            <button type="button" onclick="document.getElementById('addLocModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('locations.store') }}" class="space-y-3">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Code (UN/LOCODE)</label>
                    <input type="text" name="code" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs uppercase font-mono" placeholder="e.g. IDJKT">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Type</label>
                    <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="PORT">PORT</option>
                        <option value="AIRPORT">AIRPORT</option>
                        <option value="WAREHOUSE">WAREHOUSE</option>
                        <option value="CUSTOMS_FACILITY">CUSTOMS_FACILITY</option>
                        <option value="HUB">HUB</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Facility Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. Port of Tanjung Priok">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">City</label>
                    <input type="text" name="city" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="Jakarta">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Country</label>
                    <input type="text" name="country" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="Indonesia">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="-6.1033">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="106.8833">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addLocModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold">Save Location</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
