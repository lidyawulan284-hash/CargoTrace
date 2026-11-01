@extends('layouts.app')

@section('title', 'Security Audit Trail')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Security & Governance Audit Trail</h1>
            <p class="text-xs text-slate-500 mt-0.5">Immutable event log tracking entity mutations, logins, and operational overrides</p>
        </div>
        <span class="text-xs font-mono font-bold text-purple-700 bg-purple-50 px-3 py-1.5 rounded-xl border border-purple-200">
            Admin Governance Clearance
        </span>
    </div>

    <!-- Audit Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Timestamp</th>
                        <th class="px-4 py-3.5">Actor User</th>
                        <th class="px-4 py-3.5">Action</th>
                        <th class="px-4 py-3.5">Target Entity</th>
                        <th class="px-4 py-3.5">IP & Origin</th>
                        <th class="px-4 py-3.5">Payload Mutation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono text-[11px] text-slate-500">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-800">
                                {{ $log->user_name }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $log->action === 'DELETE' ? 'bg-rose-100 text-rose-800' : ($log->action === 'CREATE' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[10px] text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[10px]">
                                @if($log->new_values)
                                    <details class="cursor-pointer">
                                        <summary class="text-sky-600 font-semibold hover:underline">View JSON Data</summary>
                                        <pre class="bg-slate-900 text-sky-300 p-2 rounded-lg mt-1 overflow-x-auto text-[9px] max-w-xs">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </details>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No audit records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
