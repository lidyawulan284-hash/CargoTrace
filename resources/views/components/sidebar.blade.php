<!-- Desktop Sidebar (Dark Navy #172033) -->
<aside id="mainSidebar" class="hidden md:flex flex-col w-64 bg-[#172033] text-white shrink-0 min-h-screen sticky top-0 h-screen overflow-y-auto z-40 border-r border-white/10">

    <!-- Brand Logo -->
    <div class="px-5 pt-6 pb-5 flex items-center gap-3 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-[#F97316] flex items-center justify-center text-white shadow-sm group-hover:bg-[#EA580C] transition">
                <i data-lucide="ship" class="w-5 h-5"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-extrabold tracking-tight text-white leading-tight">
                    Cargo<span class="text-[#FDBA74]">Trace</span>
                </span>
                <span class="text-[10px] text-slate-400 font-medium tracking-wide">{{ __('ui.meta.brand_subtitle') }}</span>
            </div>
        </a>
    </div>

    <!-- Primary Navigation -->
    <nav class="flex-1 px-3.5 py-4 space-y-1 text-[13px] font-medium">
        <div class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-500">
            Operations
        </div>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <i data-lucide="layout-dashboard" class="w-[18px] h-[18px] shrink-0"></i>
            <span>{{ __('ui.nav.dashboard') }}</span>
        </a>

        <a href="{{ route('shipments.index') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('shipments.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <i data-lucide="container" class="w-[18px] h-[18px] shrink-0"></i>
            <span>{{ __('ui.nav.vehicle_shipments') }}</span>
        </a>

        <a href="{{ route('carriers.index') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('carriers.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <i data-lucide="anchor" class="w-[18px] h-[18px] shrink-0"></i>
            <span>{{ __('ui.nav.carriers') }}</span>
        </a>

        <a href="{{ route('locations.index') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('locations.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <i data-lucide="map-pin" class="w-[18px] h-[18px] shrink-0"></i>
            <span>{{ __('ui.nav.locations') }}</span>
        </a>

        @if(Auth::check() && Auth::user()->isAdmin())
            <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                Governance
            </div>

            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('users.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
                <i data-lucide="users" class="w-[18px] h-[18px] shrink-0"></i>
                <span>{{ __('ui.nav.users_roles') }}</span>
            </a>

            <a href="{{ route('audit_logs.index') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('audit_logs.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
                <i data-lucide="shield-check" class="w-[18px] h-[18px] shrink-0"></i>
                <span>{{ __('ui.nav.audit_logs') }}</span>
            </a>
        @endif

        <div class="px-3 pt-4 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-500">
            System
        </div>

        <a href="{{ route('tracking.public') }}" target="_blank"
           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/[0.06] transition group">
            <div class="flex items-center gap-3">
                <i data-lucide="globe" class="w-[18px] h-[18px] shrink-0"></i>
                <span>{{ __('ui.nav.public_portal') }}</span>
            </div>
            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-slate-500 group-hover:text-[#FDBA74] transition"></i>
        </a>

        <a href="{{ route('settings.index') }}"
           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('settings.*') ? 'bg-[#F97316] text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <i data-lucide="settings" class="w-[18px] h-[18px] shrink-0"></i>
            <span>{{ __('ui.nav.settings') }}</span>
        </a>
    </nav>

    <!-- Bottom Fleet Visual Card -->
    <div class="px-4 pb-5 pt-2 mt-auto">
        <div class="rounded-2xl overflow-hidden border border-white/10 relative group bg-[#0F172A]">
            <img src="{{ asset('images/roro-promo-card.png') }}" alt="{{ __('ui.nav.promo_title') }}" class="w-full h-36 object-cover block group-hover:scale-105 transition duration-500">
            <div class="absolute inset-x-0 bottom-0 p-3.5 bg-gradient-to-t from-[#172033] via-[#172033]/85 to-transparent">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-[#FDBA74] uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10A37F]"></span>
                    <span>SLA {{ config('cargotrace.delay_threshold_hours', 12) }}h Active</span>
                </div>
                <div class="text-xs font-extrabold text-white leading-tight mt-0.5">{{ __('ui.nav.promo_title') }}</div>
                <div class="text-[10px] text-slate-300 mt-0.5">{{ __('ui.nav.promo_subtitle') }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Drawer -->
<div id="mobileDrawerBackdrop" class="fixed inset-0 bg-[#172033]/60 backdrop-blur-xs z-50 hidden transition-opacity"></div>
<aside id="mobileDrawer" class="fixed inset-y-0 left-0 w-64 bg-[#172033] text-white flex flex-col z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden shadow-2xl">
    <div class="px-5 pt-5 pb-4 flex items-center justify-between border-b border-white/10">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#F97316] flex items-center justify-center text-white">
                <i data-lucide="ship" class="w-4 h-4"></i>
            </div>
            <span class="text-lg font-extrabold text-white">Cargo<span class="text-[#FDBA74]">Trace</span></span>
        </div>
        <button type="button" id="closeDrawerBtn" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <nav class="flex-1 px-3.5 py-4 space-y-1 text-sm font-medium overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i><span>{{ __('ui.nav.dashboard') }}</span>
        </a>
        <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('shipments.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
            <i data-lucide="container" class="w-4 h-4"></i><span>{{ __('ui.nav.vehicle_shipments') }}</span>
        </a>
        <a href="{{ route('carriers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('carriers.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
            <i data-lucide="anchor" class="w-4 h-4"></i><span>{{ __('ui.nav.carriers') }}</span>
        </a>
        <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('locations.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
            <i data-lucide="map-pin" class="w-4 h-4"></i><span>{{ __('ui.nav.locations') }}</span>
        </a>
        @if(Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('users.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
                <i data-lucide="users" class="w-4 h-4"></i><span>{{ __('ui.nav.users_roles') }}</span>
            </a>
            <a href="{{ route('audit_logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('audit_logs.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
                <i data-lucide="shield-check" class="w-4 h-4"></i><span>{{ __('ui.nav.audit_logs') }}</span>
            </a>
        @endif
        <a href="{{ route('tracking.public') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/5">
            <i data-lucide="globe" class="w-4 h-4"></i><span>{{ __('ui.nav.public_portal') }}</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request()->routeIs('settings.*') ? 'bg-[#F97316] text-white font-bold' : 'text-slate-300 hover:bg-white/5' }}">
            <i data-lucide="settings" class="w-4 h-4"></i><span>{{ __('ui.nav.settings') }}</span>
        </a>
    </nav>
</aside>
