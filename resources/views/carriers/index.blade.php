@extends('layouts.app')

@section('title', 'Carrier Lines Directory')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Carriers & Logistics Partners</h1>
            <p class="text-xs text-slate-500 mt-0.5">Maritime shipping lines, air freight operators, and intermodal carriers</p>
        </div>
        @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('addCarrierModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                + Add Carrier Line
            </button>
        @endif
    </div>

    <!-- Carriers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($carriers as $c)
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                            {{ $c->service_type }}
                        </span>
                        <h3 class="text-base font-black text-slate-900 mt-2">{{ $c->name }}</h3>
                        <span class="text-xs font-mono font-bold text-sky-600">Code: {{ $c->code }}</span>
                    </div>
                    <span class="w-3 h-3 rounded-full {{ $c->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}" title="{{ $c->is_active ? 'Active' : 'Inactive' }}"></span>
                </div>

                <div class="text-xs text-slate-500 space-y-1 pt-2 border-t border-slate-100">
                    <div>Email: <strong class="text-slate-700">{{ $c->contact_email ?? 'N/A' }}</strong></div>
                    <div>Phone: <strong class="text-slate-700">{{ $c->contact_phone ?? 'N/A' }}</strong></div>
                    <div>Total Shipments: <strong class="text-sky-600">{{ $c->shipments_count }} voyages</strong></div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-xs text-slate-400">
                No carriers registered.
            </div>
        @endforelse
    </div>

    @if($carriers->hasPages())
        <div class="mt-4">
            {{ $carriers->links() }}
        </div>
    @endif

</div>

<!-- Admin Add Carrier Modal -->
@if(Auth::user()->isAdmin())
<div id="addCarrierModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Register Carrier Partner</h3>
            <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('carriers.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Carrier Code (e.g. MSK, DHL, ONE)</label>
                <input type="text" name="code" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs uppercase font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Carrier Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="e.g. Maersk Line">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Primary Freight Mode</label>
                <select name="service_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <option value="SEA_FREIGHT">SEA_FREIGHT</option>
                    <option value="AIR_FREIGHT">AIR_FREIGHT</option>
                    <option value="ROAD_FREIGHT">ROAD_FREIGHT</option>
                    <option value="MULTIMODAL">MULTIMODAL</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Operations Contact Email</label>
                <input type="email" name="contact_email" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="ops@carrier.com">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Phone</label>
                <input type="text" name="contact_phone" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" placeholder="+62 ...">
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold">Save Carrier</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
