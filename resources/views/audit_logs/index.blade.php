@extends('layouts.app')

@section('title', __('ui.audit_logs.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.audit_logs') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.audit_logs.heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.audit_logs.subheading') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="text-[11px] font-bold text-[#172033] bg-[#FFF7ED] px-3 py-2 rounded-xl border border-[#FDBA74]/60 inline-flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#F97316]"></i>
                <span>{{ __('ui.audit_logs.clearance_badge') }}</span>
            </span>
            <a href="{{ route('audit_logs.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-xs transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>{{ __('ui.redesign.export_csv') }}</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.total_activities') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['total'] ?? $logs->total() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center shrink-0">
                <i data-lucide="log-in" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.login_activities') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['logins'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10A37F] flex items-center justify-center shrink-0">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.data_changes') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['changes'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#F04452] flex items-center justify-center shrink-0">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.security_events') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['security'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & View Switcher Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0] shadow-saas space-y-3">
        <form method="GET" action="{{ route('audit_logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-2.5 text-xs">
            <div class="lg:col-span-2 relative">
                <i data-lucide="search" class="w-4 h-4 text-[#718096] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.redesign.search_audit') }}"
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <select name="user_id" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.redesign.all_users') }}</option>
                @foreach($users ?? [] as $usr)
                    <option value="{{ $usr->id }}" {{ (string) request('user_id') === (string) $usr->id ? 'selected' : '' }}>{{ $usr->name }}</option>
                @endforeach
            </select>
            <select name="action" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.audit_logs.all_actions') }}</option>
                @foreach($actions ?? [] as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
            <select name="entity_type" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.audit_logs.all_entities') }}</option>
                @foreach($entityTypes ?? [] as $etype)
                    <option value="{{ $etype }}" {{ request('entity_type') === $etype ? 'selected' : '' }}>{{ $etype }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#172033] hover:bg-[#1F2B44] text-white rounded-xl font-bold transition cursor-pointer">{{ __('ui.common.filter') }}</button>
                <a href="{{ route('audit_logs.index') }}" class="py-2 px-3 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] rounded-xl font-semibold transition">{{ __('ui.common.reset') }}</a>
            </div>
        </form>

        <div class="flex items-center justify-end">
            <div class="inline-flex items-center rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] p-1 text-xs font-bold">
                <button type="button" id="btnAuditTable" onclick="setAuditView('table')" class="px-3 py-1 rounded-lg bg-[#172033] text-white transition cursor-pointer">{{ __('ui.redesign.view_table') }}</button>
                <button type="button" id="btnAuditFeed" onclick="setAuditView('feed')" class="px-3 py-1 rounded-lg text-[#718096] hover:text-[#172033] transition cursor-pointer">{{ __('ui.redesign.view_feed') }}</button>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div id="auditTableView" class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F5F9] text-[#718096] uppercase text-[10px] font-bold tracking-wider border-b border-[#E6EAF0]">
                    <tr>
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-4 py-3.5">{{ __('ui.audit_logs.col_actor') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.audit_logs.col_action') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.audit_logs.col_entity') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.audit_logs.col_ip') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.audit_logs.col_timestamp') }}</th>
                        <th class="px-5 py-3.5 text-right">{{ __('ui.audit_logs.col_diff') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6EAF0] font-medium text-[#172033]">
                    @forelse($logs as $log)
                        @php
                            $actionClass = match(true) {
                                str_contains($log->action, 'DELETE') => 'bg-red-50 text-[#F04452] border-red-200',
                                str_starts_with($log->action, 'CREATE') || $log->action === 'REGISTER' => 'bg-emerald-50 text-[#10A37F] border-emerald-200',
                                str_contains($log->action, 'LOGIN') => 'bg-blue-50 text-[#3B82F6] border-blue-200',
                                default => 'bg-[#FFF7ED] text-[#F97316] border-[#FDBA74]/60',
                            };
                        @endphp
                        <tr class="hover:bg-[#F4F5F9]/60 transition">
                            <td class="px-5 py-3.5 font-mono text-[11px] text-[#718096]">#{{ $log->id }}</td>
                            <td class="px-4 py-3.5 font-bold text-[#172033]">{{ $log->user_name }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase border {{ $actionClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[#172033]">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                            <td class="px-4 py-3.5 font-mono text-[11px] text-[#718096]">{{ $log->ip_address }}</td>
                            <td class="px-4 py-3.5 font-mono text-[11px] text-[#718096]">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button"
                                        onclick="openAuditDetailModal({{ $log->id }}, @js($log->user_name), @js($log->action), @js($log->entity_type . ' #' . $log->entity_id), @js($log->ip_address), @js($log->created_at->format('Y-m-d H:i:s')), @js($log->old_values), @js($log->new_values))"
                                        class="px-2.5 py-1 rounded-lg bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white font-bold transition cursor-pointer">
                                    {{ __('ui.audit_logs.view_diff') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-[#718096]">{{ __('ui.audit_logs.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-[#E6EAF0] bg-[#F4F5F9]/40">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Activity Timeline Feed View -->
    <div id="auditFeedView" class="hidden bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-6">
        <div class="relative pl-6 space-y-5 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#E6EAF0]">
            @forelse($logs as $log)
                <div class="relative text-xs">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-[#F97316] border-2 border-white shadow-xs"></div>
                    <div class="p-4 rounded-xl bg-[#F4F5F9]/60 border border-[#E6EAF0] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <div class="font-bold text-[#172033]">
                                {{ $log->user_name }}
                                <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-white border border-[#E6EAF0] text-[#F97316] mx-1">{{ $log->action }}</span>
                                <span class="font-mono text-[#718096]">{{ $log->entity_type }} #{{ $log->entity_id }}</span>
                            </div>
                            <div class="text-[11px] text-[#718096] mt-1 font-mono">IP: {{ $log->ip_address }} &bull; {{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <button type="button"
                                onclick="openAuditDetailModal({{ $log->id }}, @js($log->user_name), @js($log->action), @js($log->entity_type . ' #' . $log->entity_id), @js($log->ip_address), @js($log->created_at->format('Y-m-d H:i:s')), @js($log->old_values), @js($log->new_values))"
                                class="px-3 py-1.5 rounded-lg bg-white border border-[#E6EAF0] hover:border-[#F97316] text-[#172033] font-bold transition cursor-pointer self-start">
                            {{ __('ui.redesign.activity_details') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-[#718096] text-xs">{{ __('ui.audit_logs.empty') }}</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Audit Activity Details Modal -->
<div id="auditDetailModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-lg w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]" id="auditDetailTitle">{{ __('ui.redesign.activity_details') }}</h3>
            <button type="button" onclick="document.getElementById('auditDetailModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-2 text-[11px] bg-[#F4F5F9] p-3 rounded-xl border border-[#E6EAF0]">
            <div><span class="text-[#718096] block">{{ __('ui.audit_logs.col_actor') }}</span><strong id="adActor" class="text-[#172033]"></strong></div>
            <div><span class="text-[#718096] block">{{ __('ui.audit_logs.col_action') }}</span><strong id="adAction" class="font-mono text-[#F97316]"></strong></div>
            <div><span class="text-[#718096] block">{{ __('ui.audit_logs.col_entity') }}</span><strong id="adEntity" class="font-mono text-[#172033]"></strong></div>
            <div><span class="text-[#718096] block">{{ __('ui.audit_logs.col_timestamp') }}</span><strong id="adTime" class="font-mono text-[#172033]"></strong></div>
        </div>
        <div class="space-y-2.5">
            <div>
                <span class="text-[10px] uppercase font-bold text-[#F04452] block mb-1">{{ __('ui.audit_logs.old_label') }}</span>
                <pre id="adOld" class="bg-[#172033] text-rose-300 p-3 rounded-xl overflow-x-auto text-[10px] max-h-40"></pre>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-[#10A37F] block mb-1">{{ __('ui.audit_logs.new_label') }}</span>
                <pre id="adNew" class="bg-[#172033] text-emerald-300 p-3 rounded-xl overflow-x-auto text-[10px] max-h-40"></pre>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" onclick="document.getElementById('auditDetailModal').classList.add('hidden')" class="px-4 py-2 bg-[#172033] text-white font-bold rounded-xl cursor-pointer">{{ __('ui.common.close') }}</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setAuditView(mode) {
    const tableEl = document.getElementById('auditTableView');
    const feedEl = document.getElementById('auditFeedView');
    const btnTable = document.getElementById('btnAuditTable');
    const btnFeed = document.getElementById('btnAuditFeed');

    if (mode === 'feed') {
        tableEl?.classList.add('hidden');
        feedEl?.classList.remove('hidden');
        btnFeed?.classList.add('bg-[#172033]', 'text-white');
        btnFeed?.classList.remove('text-[#718096]');
        btnTable?.classList.remove('bg-[#172033]', 'text-white');
        btnTable?.classList.add('text-[#718096]');
    } else {
        feedEl?.classList.add('hidden');
        tableEl?.classList.remove('hidden');
        btnTable?.classList.add('bg-[#172033]', 'text-white');
        btnTable?.classList.remove('text-[#718096]');
        btnFeed?.classList.remove('bg-[#172033]', 'text-white');
        btnFeed?.classList.add('text-[#718096]');
    }
}

function openAuditDetailModal(id, actor, action, entity, ip, time, oldVal, newVal) {
    document.getElementById('auditDetailTitle').innerText = `${@json(__('ui.redesign.activity_details'))} #${id}`;
    document.getElementById('adActor').innerText = actor;
    document.getElementById('adAction').innerText = action;
    document.getElementById('adEntity').innerText = entity;
    document.getElementById('adTime').innerText = `${time} (${ip || 'local'})`;
    document.getElementById('adOld').textContent = oldVal ? JSON.stringify(oldVal, null, 2) : 'null';
    document.getElementById('adNew').textContent = newVal ? JSON.stringify(newVal, null, 2) : 'null';
    document.getElementById('auditDetailModal').classList.remove('hidden');
}
</script>
@endpush
