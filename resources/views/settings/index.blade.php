@extends('layouts.app')

@section('title', __('ui.settings.title'))

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#CBD5E1]"></i>
    <span class="font-semibold text-[#172033]">{{ __('ui.nav.settings') }}</span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-[#172033] tracking-tight">{{ __('ui.settings.heading') }}</h1>
        <p class="text-xs sm:text-sm text-[#718096] mt-0.5">{{ __('ui.settings.subheading') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Settings Navigation Tabs -->
        <aside class="lg:col-span-3 bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-3 space-y-1 text-xs font-semibold">
            @php
                $tabs = [
                    'general' => ['icon' => 'sliders-horizontal', 'label' => __('ui.settings.tab_general')],
                    'profile' => ['icon' => 'user', 'label' => __('ui.settings.tab_profile')],
                    'account' => ['icon' => 'id-card', 'label' => __('ui.settings.tab_account')],
                    'notifications' => ['icon' => 'bell', 'label' => __('ui.settings.tab_notifications')],
                    'language' => ['icon' => 'globe', 'label' => __('ui.settings.tab_language')],
                    'security' => ['icon' => 'shield-check', 'label' => __('ui.settings.tab_security')],
                    'preferences' => ['icon' => 'monitor-cog', 'label' => __('ui.settings.tab_preferences')],
                ];
            @endphp

            @foreach($tabs as $tabKey => $tabInfo)
                <a href="{{ route('settings.index', ['tab' => $tabKey]) }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ $activeTab === $tabKey ? 'bg-[#FFF7ED] text-[#F97316] font-bold border border-[#FDBA74]/50' : 'text-[#718096] hover:text-[#172033] hover:bg-[#F4F5F9]' }}">
                    <i data-lucide="{{ $tabInfo['icon'] }}" class="w-4 h-4 shrink-0"></i>
                    <span>{{ $tabInfo['label'] }}</span>
                </a>
            @endforeach
        </aside>

        <!-- Right Settings Content Panel -->
        <section class="lg:col-span-9 bg-white rounded-2xl border border-[#E6EAF0] shadow-saas p-6 space-y-6">

            @if($activeTab === 'general')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_general') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">CargoTrace deterministic rules, SLA thresholds, and external service status</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.sla_title') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.sla_subtitle') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-[#FFF7ED] text-[#F97316] font-bold border border-[#FDBA74]/60">
                            {{ __('ui.settings.hours_value', ['hours' => config('cargotrace.delay_threshold_hours', 12)]) }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.severity_title') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.severity_subtitle') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[#10A37F] font-bold">
                            {{ __('ui.settings.active') }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.weather_title') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.weather_subtitle') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#3B82F6] font-bold">
                            {{ __('ui.settings.enabled') }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.geocoding_title') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.geocoding_subtitle') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[#10A37F] font-bold">
                            {{ __('ui.settings.enabled') }}
                        </span>
                    </div>
                </div>

            @elseif($activeTab === 'profile')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_profile') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">Update your personal contact details and organization name</p>
                </div>

                <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-4 max-w-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.users.field_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.users.field_company') }}</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.users.field_phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                            {{ __('ui.common.save_changes') }}
                        </button>
                    </div>
                </form>

            @elseif($activeTab === 'account')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_account') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">Account identity, role assignment, and Sanctum API readiness</p>
                </div>

                <div class="space-y-3 max-w-xl text-xs">
                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex justify-between items-center">
                        <span class="text-[#718096] font-medium">{{ __('ui.users.col_email') }}</span>
                        <strong class="font-mono text-[#172033]">{{ $user->email }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex justify-between items-center">
                        <span class="text-[#718096] font-medium">{{ __('ui.users.col_role') }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#172033] text-white uppercase">{{ __('ui.roles.' . $user->role) }}</span>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex justify-between items-center">
                        <span class="text-[#718096] font-medium">REST API Authentication</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-[#10A37F]">Laravel Sanctum Bearer Token</span>
                    </div>
                </div>

            @elseif($activeTab === 'notifications')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_notifications') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">Control which operational events appear in your notification center</p>
                </div>

                <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-4 max-w-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="tab" value="notifications">

                    <label class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] cursor-pointer">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.notify_status_label') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.notify_status_desc') }}</div>
                        </div>
                        <input type="checkbox" name="notify_status_updates" value="1" {{ !empty($preferences['notify_status_updates']) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                    </label>

                    <label class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] cursor-pointer">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.notify_exceptions_label') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.notify_exceptions_desc') }}</div>
                        </div>
                        <input type="checkbox" name="notify_exceptions" value="1" {{ !empty($preferences['notify_exceptions']) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                    </label>

                    <label class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] cursor-pointer">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.notify_delay_label') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.notify_delay_desc') }}</div>
                        </div>
                        <input type="checkbox" name="notify_delay_alerts" value="1" {{ !empty($preferences['notify_delay_alerts']) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                    </label>

                    <button type="submit" class="px-5 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                        {{ __('ui.common.save_changes') }}
                    </button>
                </form>

            @elseif($activeTab === 'language')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_language') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">{{ __('ui.settings.language_subtitle') }}</p>
                </div>

                <div class="p-5 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] flex flex-col sm:flex-row sm:items-center justify-between gap-4 max-w-xl">
                    <div>
                        <div class="text-sm font-bold text-[#172033]">{{ __('ui.settings.language_title') }}</div>
                        <div class="text-xs text-[#718096] mt-0.5">{{ __('ui.settings.language_subtitle') }}</div>
                    </div>
                    <x-language-switcher />
                </div>

            @elseif($activeTab === 'security')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_security') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">Update your account password (minimum 8 characters, bcrypt hashed)</p>
                </div>

                <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4 max-w-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.settings.current_password') }}</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.settings.new_password') }}</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                        </div>
                        <div>
                            <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.settings.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" required minlength="8"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                        {{ __('ui.settings.update_password_btn') }}
                    </button>
                </form>

            @elseif($activeTab === 'preferences')
                <div class="border-b border-[#E6EAF0] pb-4">
                    <h2 class="text-base font-extrabold text-[#172033]">{{ __('ui.settings.tab_preferences') }}</h2>
                    <p class="text-xs text-[#718096] mt-0.5">Personalize dashboard telemetry behavior and display density</p>
                </div>

                <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-4 max-w-xl text-xs">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="tab" value="preferences">

                    <label class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] cursor-pointer">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.compact_tables_label') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.compact_tables_desc') }}</div>
                        </div>
                        <input type="checkbox" name="compact_tables" value="1" {{ !empty($preferences['compact_tables']) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                    </label>

                    <label class="flex items-start justify-between gap-4 p-4 rounded-xl bg-[#F4F5F9] border border-[#E6EAF0] cursor-pointer">
                        <div>
                            <div class="font-bold text-[#172033]">{{ __('ui.settings.auto_follow_label') }}</div>
                            <div class="text-[11px] text-[#718096] mt-0.5">{{ __('ui.settings.auto_follow_desc') }}</div>
                        </div>
                        <input type="checkbox" name="auto_follow_map" value="1" {{ !empty($preferences['auto_follow_map']) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]">
                    </label>

                    <div>
                        <label class="block font-bold text-[#172033] mb-1.5">{{ __('ui.settings.timezone_label') }}</label>
                        <select name="timezone" class="w-full px-3.5 py-2.5 rounded-xl bg-[#F4F5F9] focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] focus:outline-none text-sm">
                            @foreach(['UTC', 'Asia/Jakarta', 'Asia/Singapore', 'Europe/Amsterdam', 'Europe/Berlin'] as $tz)
                                <option value="{{ $tz }}" {{ ($preferences['timezone'] ?? 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                        {{ __('ui.common.save_changes') }}
                    </button>
                </form>
            @endif

        </section>
    </div>

</div>
@endsection
