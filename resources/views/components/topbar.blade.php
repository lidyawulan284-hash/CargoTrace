<header class="bg-white border-b border-[#E6EAF0] h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 gap-3">
    <!-- Left: Mobile Toggle & Global Search -->
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <button type="button" id="hamburgerBtn" class="md:hidden p-2 text-[#718096] hover:text-[#172033] hover:bg-[#F4F5F9] rounded-xl transition" title="{{ __('ui.header.toggle_navigation') }}">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <form action="{{ route('shipments.index') }}" method="GET" class="relative w-full max-w-md">
            <div class="relative flex items-center">
                <span class="absolute left-3.5 text-[#718096] pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('ui.header.search_placeholder') }}"
                       class="w-full bg-[#F4F5F9] hover:bg-white focus:bg-white border border-[#E6EAF0] focus:border-[#F97316] rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-[#172033] placeholder-[#718096] outline-none transition focus:ring-2 focus:ring-[#F97316]/15">
            </div>
        </form>
    </div>

    <!-- Right: Language Switcher, Notification Bell & User Profile -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <x-language-switcher />

        <a href="{{ route('notifications.index') }}"
           class="relative p-2 text-[#718096] hover:text-[#172033] hover:bg-[#F4F5F9] rounded-xl border border-transparent hover:border-[#E6EAF0] transition flex items-center justify-center"
           title="{{ __('ui.header.notifications') }}">
            <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
            @php
                $unread = Auth::check() ? Auth::user()->notifications()->whereNull('read_at')->count() : 0;
            @endphp
            @if($unread > 0)
                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-[#F04452] text-[10px] font-bold text-white rounded-full flex items-center justify-center ring-2 ring-white">
                    {{ $unread }}
                </span>
            @endif
        </a>

        <div class="h-6 w-px bg-[#E6EAF0] hidden sm:block"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" id="userMenuContainer">
            <button type="button" id="userMenuBtn" class="flex items-center gap-2.5 p-1 pr-2 rounded-xl hover:bg-[#F4F5F9] transition focus:outline-none cursor-pointer">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#172033] text-white font-bold text-xs flex items-center justify-center ring-2 ring-[#FDBA74]/50 shrink-0">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'GT' }}
                </div>
                <div class="hidden sm:flex flex-col text-left">
                    <span class="text-xs font-bold text-[#172033] flex items-center gap-1 leading-tight">
                        {{ Auth::check() ? Auth::user()->name : __('ui.header.guest') }}
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#718096]"></i>
                    </span>
                    <span class="text-[10px] text-[#718096] font-medium leading-none mt-0.5">
                        {{ Auth::check() ? __('ui.roles.' . Auth::user()->role) : __('ui.header.visitor') }}
                    </span>
                </div>
            </button>

            <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-60 bg-white border border-[#E6EAF0] rounded-2xl shadow-saas-md py-2 z-50 text-xs text-[#172033]">
                <div class="px-4 py-2.5 border-b border-[#E6EAF0]">
                    <p class="font-bold text-[#172033] truncate">{{ Auth::check() ? Auth::user()->name : __('ui.header.user') }}</p>
                    <p class="text-[11px] text-[#718096] truncate">{{ Auth::check() ? Auth::user()->email : '' }}</p>
                </div>

                <div class="px-2 py-1.5 border-b border-[#E6EAF0]">
                    <a href="{{ route('settings.index', ['tab' => 'profile']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-[#F4F5F9] font-semibold text-[#172033] transition">
                        <i data-lucide="user-cog" class="w-4 h-4 text-[#718096]"></i>
                        <span>{{ __('ui.settings.tab_profile') }}</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-[#F4F5F9] font-semibold text-[#172033] transition">
                        <i data-lucide="settings" class="w-4 h-4 text-[#718096]"></i>
                        <span>{{ __('ui.nav.settings') }}</span>
                    </a>
                </div>

                <div class="px-2 py-1.5 border-b border-[#E6EAF0]">
                    <div class="px-3 py-1 text-[10px] font-bold text-[#718096] uppercase tracking-wider">{{ __('ui.header.quick_role_switch') }}</div>
                    <a href="{{ route('demo.login', 'admin') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-[#FFF7ED] font-medium text-[#172033] hover:text-[#F97316]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#172033]"></span>
                        <span>{{ __('ui.header.role_admin') }}</span>
                    </a>
                    <a href="{{ route('demo.login', 'operator') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-[#FFF7ED] font-medium text-[#172033] hover:text-[#F97316]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6]"></span>
                        <span>{{ __('ui.header.role_operator') }}</span>
                    </a>
                    <a href="{{ route('demo.login', 'customer') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-[#FFF7ED] font-medium text-[#172033] hover:text-[#F97316]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#F97316]"></span>
                        <span>{{ __('ui.header.role_customer') }}</span>
                    </a>
                </div>

                <div class="px-2 pt-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-[#F04452] hover:bg-red-50 font-semibold transition flex items-center gap-2 cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>{{ __('ui.header.log_out') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
