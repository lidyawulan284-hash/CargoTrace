@extends('layouts.app')

@section('title', __('ui.users.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.users_roles') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.users.heading') }}</h1>
            <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.users.subheading') }}</p>
        </div>
        <button type="button" onclick="document.getElementById('addUserModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer self-start">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>{{ __('ui.users.add_btn') }}</span>
        </button>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#F97316] flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.total_users') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['total'] ?? $users->total() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#10A37F] flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.active_users') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#172033]/5 text-[#172033] flex items-center justify-center shrink-0">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.administrators') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['admins'] ?? 0 }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center shrink-0">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-medium text-[#718096]">{{ __('ui.redesign.other_roles') }}</div>
                <div class="text-xl font-extrabold text-[#172033]">{{ $stats['others'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-[#E6EAF0] shadow-saas">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div class="relative flex-1 min-w-[220px]">
                <i data-lucide="search" class="w-4 h-4 text-[#718096] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.users.search_placeholder') }}"
                       class="w-full pl-9 pr-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
            </div>
            <select name="role" class="px-3.5 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none">
                <option value="">{{ __('ui.users.all_roles') }}</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('ui.roles.admin') }}</option>
                <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>{{ __('ui.roles.operator') }}</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>{{ __('ui.roles.customer') }}</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-[#172033] text-white rounded-xl font-bold hover:bg-[#1F2B44] transition cursor-pointer">{{ __('ui.common.filter') }}</button>
            <a href="{{ route('users.index') }}" class="px-3.5 py-2 bg-[#F4F5F9] text-[#172033] rounded-xl font-semibold hover:bg-[#E6EAF0] transition">{{ __('ui.common.reset') }}</a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F4F5F9] text-[#718096] uppercase text-[10px] font-bold tracking-wider border-b border-[#E6EAF0]">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('ui.users.col_user_company') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.users.col_email') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.users.col_role') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.redesign.col_permissions') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.redesign.col_status') }}</th>
                        <th class="px-4 py-3.5">{{ __('ui.redesign.col_last_login') }}</th>
                        <th class="px-5 py-3.5 text-right">{{ __('ui.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6EAF0] font-medium text-[#172033]">
                    @forelse($users as $u)
                        @php
                            $roleBadge = match($u->role) {
                                'admin' => 'bg-[#172033] text-white border-[#172033]',
                                'operator' => 'bg-blue-50 text-[#3B82F6] border-blue-200',
                                default => 'bg-[#FFF7ED] text-[#F97316] border-[#FDBA74]/60',
                            };
                            $permSummary = match($u->role) {
                                'admin' => 'Full Platform & Governance',
                                'operator' => 'Shipments, Tracking & Exceptions',
                                default => 'Own Consignments Read-Only',
                            };
                            $lastLoginAt = isset($lastLogins[$u->id]) ? \Carbon\Carbon::parse($lastLogins[$u->id])->diffForHumans() : __('ui.redesign.never_logged_in');
                        @endphp
                        <tr class="hover:bg-[#F4F5F9]/60 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#172033] text-[#FDBA74] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#172033]">{{ $u->name }}</div>
                                        <div class="text-[11px] text-[#718096]">{{ $u->company_name ?? __('ui.users.independent_account') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[#718096]">{{ $u->email }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                                    {{ __('ui.roles.' . $u->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-[#718096]">{{ $permSummary }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-[#10A37F]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#10A37F]"></span>
                                    {{ __('ui.common.active') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-[11px] text-[#718096]">{{ $lastLoginAt }}</td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <button type="button"
                                        onclick="openUserDetailsModal(@js($u->name), @js($u->email), @js($u->role), @js(__('ui.roles.' . $u->role)), @js($u->company_name ?? __('ui.users.independent_account')), @js($u->phone ?? __('ui.common.na')), {{ $u->shipments_count }}, @js($lastLoginAt))"
                                        class="px-2.5 py-1 bg-[#FFF7ED] text-[#F97316] hover:bg-[#F97316] hover:text-white rounded-lg font-bold transition cursor-pointer">
                                    {{ __('ui.common.details') }}
                                </button>
                                <button type="button"
                                        onclick="openEditUserModal({{ $u->id }}, @js($u->name), @js($u->role), @js($u->company_name), @js($u->phone))"
                                        class="px-2.5 py-1 bg-[#F4F5F9] hover:bg-[#E6EAF0] text-[#172033] rounded-lg font-bold transition cursor-pointer">
                                    {{ __('ui.users.edit_role') }}
                                </button>
                                @if($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm(@js(__('ui.users.confirm_delete', ['email' => $u->email])));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-[#F04452] text-[#F04452] hover:text-white rounded-lg font-bold transition cursor-pointer">
                                            {{ __('ui.common.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-[#718096]">{{ __('ui.users.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#E6EAF0] bg-[#F4F5F9]/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- User Details & Permissions Modal -->
<div id="userDetailsModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.redesign.permissions_modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('userDetailsModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.field_name') }}</span><strong class="text-[#172033]" id="udName"></strong></div>
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.col_email') }}</span><strong class="font-mono text-[#172033]" id="udEmail"></strong></div>
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.col_role') }}</span><strong class="text-[#F97316]" id="udRole"></strong></div>
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.field_company') }}</span><strong class="text-[#172033]" id="udCompany"></strong></div>
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.col_phone') }}</span><strong class="text-[#172033]" id="udPhone"></strong></div>
            <div class="flex justify-between py-1 border-b border-[#E6EAF0]"><span class="text-[#718096]">{{ __('ui.users.col_shipments') }}</span><strong class="text-[#172033]" id="udShipments"></strong></div>
            <div class="flex justify-between py-1"><span class="text-[#718096]">{{ __('ui.redesign.col_last_login') }}</span><strong class="text-[#10A37F]" id="udLastLogin"></strong></div>
        </div>
        <div class="p-3 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] space-y-1.5">
            <div class="text-[10px] font-bold uppercase tracking-wider text-[#718096]">{{ __('ui.redesign.col_permissions') }}</div>
            <ul id="udPermList" class="space-y-1 text-[#172033] font-medium"></ul>
        </div>
        <div class="flex justify-end">
            <button type="button" onclick="document.getElementById('userDetailsModal').classList.add('hidden')" class="px-4 py-2 bg-[#172033] text-white font-bold rounded-xl cursor-pointer">{{ __('ui.common.close') }}</button>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.users.create_modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_name') }}</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_email') }}</label>
                <input type="email" name="email" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_role') }}</label>
                    <select name="role" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                        <option value="customer">{{ __('ui.roles.customer') }}</option>
                        <option value="operator">{{ __('ui.roles.operator') }}</option>
                        <option value="admin">{{ __('ui.roles.admin') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_password') }}</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                </div>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_company_oem') }}</label>
                <input type="text" name="company_name" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="e.g. PT Nusantara EV Motors">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_phone') }}</label>
                <input type="text" name="phone" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]" placeholder="+62 ...">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.users.create_btn') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-2xl max-w-md w-full p-6 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#E6EAF0] pb-3">
            <h3 class="text-base font-extrabold text-[#172033]">{{ __('ui.users.edit_modal_title') }}</h3>
            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-[#718096] hover:text-[#172033]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editUserForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_name') }}</label>
                <input type="text" name="name" id="editUserName" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_role') }}</label>
                <select name="role" id="editUserRole" required class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
                    <option value="customer">{{ __('ui.roles.customer') }}</option>
                    <option value="operator">{{ __('ui.roles.operator') }}</option>
                    <option value="admin">{{ __('ui.roles.admin') }}</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_company') }}</label>
                <input type="text" name="company_name" id="editUserCompany" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div>
                <label class="block font-bold text-[#172033] mb-1">{{ __('ui.users.field_phone') }}</label>
                <input type="text" name="phone" id="editUserPhone" class="w-full px-3 py-2 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0]">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 border border-[#E6EAF0] rounded-xl font-semibold text-[#718096]">{{ __('ui.common.cancel') }}</button>
                <button type="submit" class="px-5 py-2 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold cursor-pointer">{{ __('ui.common.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const ROLE_PERMS = {
    admin: ['Manage Users & Roles', 'Manage Carriers & Auto Hubs', 'Full Shipments CRUD & Soft-Delete', 'Append Tracking & Resolve Exceptions', 'View & Export Security Audit Logs'],
    operator: ['Create & Update Shipments', 'Append Validated Tracking Events', 'Flag, Investigate & Resolve Exceptions', 'Geocode Hub Coordinates', 'Read-Only Access to Carriers & Ports'],
    customer: ['View Own Shipments Only (403 on Others)', 'Track Route Map, Timeline & Weather', 'Receive In-App Status & Exception Notifications']
};

function openUserDetailsModal(name, email, roleKey, roleLabel, company, phone, shipments, lastLogin) {
    document.getElementById('udName').innerText = name;
    document.getElementById('udEmail').innerText = email;
    document.getElementById('udRole').innerText = roleLabel;
    document.getElementById('udCompany').innerText = company;
    document.getElementById('udPhone').innerText = phone;
    document.getElementById('udShipments').innerText = shipments;
    document.getElementById('udLastLogin').innerText = lastLogin;

    const list = document.getElementById('udPermList');
    list.innerHTML = '';
    (ROLE_PERMS[roleKey] || ROLE_PERMS.customer).forEach(perm => {
        const li = document.createElement('li');
        li.textContent = '• ' + perm;
        list.appendChild(li);
    });

    document.getElementById('userDetailsModal').classList.remove('hidden');
}

function openEditUserModal(id, name, role, company, phone) {
    document.getElementById('editUserForm').action = '/users/' + id;
    document.getElementById('editUserName').value = name || '';
    document.getElementById('editUserRole').value = role || 'customer';
    document.getElementById('editUserCompany').value = company || '';
    document.getElementById('editUserPhone').value = phone || '';
    document.getElementById('editUserModal').classList.remove('hidden');
}
</script>
@endpush
@endsection
