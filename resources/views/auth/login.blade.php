<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F5F9]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.auth.login_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-[#F4F5F9] text-[#172033] antialiased flex flex-col justify-between font-sans selection:bg-[#F97316] selection:text-white">

    <!-- Top Header -->
    <header class="w-full bg-white border-b border-[#E6EAF0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-[#172033] flex items-center justify-center text-[#F97316] shadow-xs">
                    <i data-lucide="ship" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-extrabold tracking-tight text-[#172033] leading-none">
                        Cargo<span class="text-[#F97316]">Trace</span>
                    </span>
                    <span class="text-[9.5px] uppercase font-bold tracking-widest text-[#718096] mt-0.5">
                        {{ strtoupper(__('ui.meta.global_platform')) }}
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('tracking.public') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-[#718096] hover:text-[#F97316] transition">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>{{ __('ui.auth.track_without_login') }}</span>
                </a>
                <x-language-switcher />
            </div>
        </div>
    </header>

    <!-- Main Split Layout -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            <!-- Left Editorial Showcase -->
            <section class="lg:col-span-7 space-y-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FFF7ED] border border-[#FDBA74]/60 text-[11px] font-bold text-[#172033]">
                    <span class="w-2 h-2 rounded-full bg-[#F97316]"></span>
                    <span>{{ __('ui.auth.hero_badge') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-[44px] font-extrabold text-[#172033] tracking-tight leading-[1.08]">
                    {{ __('ui.auth.hero_heading_1') }}<br>
                    {{ __('ui.auth.hero_heading_2') }} <span class="text-[#F97316]">{{ __('ui.auth.hero_heading_highlight') }}</span>
                </h1>

                <p class="text-sm sm:text-base text-[#718096] max-w-xl leading-relaxed">
                    {{ __('ui.auth.hero_description') }}
                </p>

                <!-- Clean Photographic Card -->
                <div class="relative rounded-2xl overflow-hidden border border-[#E6EAF0] bg-[#172033] shadow-[0_8px_24px_rgba(23,32,51,0.08)]">
                    <img src="{{ asset('images/dashboard-hero-roro.jpg') }}"
                         onerror="this.src='{{ asset('images/roro-promo-card.png') }}'"
                         alt="CargoTrace International Ro-Ro Vessel"
                         class="w-full h-64 sm:h-72 object-cover opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#172033] via-[#172033]/25 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 bg-white/95 backdrop-blur-xs px-3.5 py-2 rounded-xl border border-[#E6EAF0]">
                            <span class="w-2 h-2 rounded-full bg-[#10A37F]"></span>
                            <span class="text-[11px] font-bold text-[#172033]">{{ __('ui.auth.plane_badge') }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-[#172033]/90 text-white px-3.5 py-2 rounded-xl border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-[#F97316]"></span>
                            <span class="text-[11px] font-semibold">{{ __('ui.auth.pallet_badge') }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Right Login Card -->
            <section class="lg:col-span-5 flex flex-col items-center">
                <div class="w-full max-w-md bg-white rounded-2xl border border-[#E6EAF0] shadow-[0_8px_30px_rgba(23,32,51,0.06)] p-6 sm:p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#172033] text-[#F97316] mb-3.5">
                            <i data-lucide="truck" class="w-6 h-6"></i>
                        </div>
                        <h2 class="text-xl font-extrabold text-[#172033] tracking-tight">
                            {{ __('ui.auth.card_title_prefix') }} <span class="text-[#F97316]">CargoTrace</span>
                        </h2>
                        <p class="text-xs text-[#718096] mt-1">
                            {{ __('ui.auth.card_subtitle') }}
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-emerald-50 border border-[#10A37F]/30 p-3 text-xs text-[#172033]">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-[#10A37F] shrink-0 mt-0.5"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-blue-50 border border-[#3B82F6]/30 p-3 text-xs text-[#172033]">
                            <i data-lucide="info" class="w-4 h-4 text-[#3B82F6] shrink-0 mt-0.5"></i>
                            <span class="font-medium">{{ session('info') }}</span>
                        </div>
                    @endif
                    @if(session('error') || (isset($errors) && $errors->any()))
                        <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-red-50 border border-[#F04452]/30 p-3 text-xs text-[#F04452]">
                            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                            <span class="font-medium">{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-xs font-semibold text-[#172033] mb-1.5">{{ __('ui.auth.email_label') }}</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#718096]">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </span>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] placeholder-[#718096] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                                       placeholder="{{ __('ui.auth.email_placeholder') }}">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-semibold text-[#172033]">{{ __('ui.auth.password_label') }}</label>
                                <span class="text-[10px] text-[#F97316] font-bold bg-[#FFF7ED] px-2 py-0.5 rounded-md border border-[#FDBA74]/50">
                                    {{ __('ui.auth.demo_password_hint') }} <span class="font-mono">password</span>
                                </span>
                            </div>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#718096]">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="password" name="password" required autocomplete="current-password"
                                       class="w-full pl-10 pr-10 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] placeholder-[#718096] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                                       placeholder="{{ __('ui.auth.password_placeholder') }}">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#718096] hover:text-[#172033]" aria-label="{{ __('ui.auth.toggle_password_aria') }}">
                                    <i id="eyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                                    <i id="eyeSlashIcon" data-lucide="eye-off" class="w-4 h-4 hidden"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-2 cursor-pointer text-[#718096] hover:text-[#172033]">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E6EAF0] text-[#F97316] focus:ring-[#F97316]/20">
                                <span>{{ __('ui.auth.remember_me') }}</span>
                            </label>
                            <a href="{{ route('tracking.public') }}" class="font-semibold text-[#F97316] hover:text-[#EA580C] flex items-center gap-1">
                                <span>{{ __('ui.auth.track_without_login') }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold text-sm shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>{{ __('ui.auth.sign_in_btn') }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <!-- Quick Evaluator Demo Accounts -->
                    <div class="relative my-5">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#E6EAF0]"></div></div>
                        <div class="relative flex justify-center text-[10px] uppercase tracking-wider text-[#718096] font-bold">
                            <span class="bg-white px-2.5">{{ __('ui.auth.quick_demo_divider') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="fillDemoCredentials('admin@cargotrace.test', 'password', 'admin')" id="demo-admin-btn"
                                class="demo-role-btn relative flex flex-col items-center justify-center p-2.5 rounded-xl bg-[#F4F5F9] hover:bg-[#FFF7ED] border border-[#E6EAF0] hover:border-[#F97316] transition cursor-pointer">
                            <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#F97316] text-white rounded-full text-[9px] font-bold flex items-center justify-center">✓</span>
                            <i data-lucide="shield-check" class="w-4 h-4 text-[#172033] mb-1"></i>
                            <span class="text-xs font-bold text-[#172033]">Admin</span>
                            <span class="text-[9px] text-[#718096]">{{ __('ui.auth.demo_admin_sub') }}</span>
                        </button>
                        <button type="button" onclick="fillDemoCredentials('operator@cargotrace.test', 'password', 'operator')" id="demo-operator-btn"
                                class="demo-role-btn relative flex flex-col items-center justify-center p-2.5 rounded-xl bg-[#F4F5F9] hover:bg-[#FFF7ED] border border-[#E6EAF0] hover:border-[#F97316] transition cursor-pointer">
                            <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#F97316] text-white rounded-full text-[9px] font-bold flex items-center justify-center">✓</span>
                            <i data-lucide="route" class="w-4 h-4 text-[#3B82F6] mb-1"></i>
                            <span class="text-xs font-bold text-[#172033]">Operator</span>
                            <span class="text-[9px] text-[#718096]">{{ __('ui.auth.demo_operator_sub') }}</span>
                        </button>
                        <button type="button" onclick="fillDemoCredentials('customer@cargotrace.test', 'password', 'customer')" id="demo-customer-btn"
                                class="demo-role-btn relative flex flex-col items-center justify-center p-2.5 rounded-xl bg-[#F4F5F9] hover:bg-[#FFF7ED] border border-[#E6EAF0] hover:border-[#F97316] transition cursor-pointer">
                            <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#F97316] text-white rounded-full text-[9px] font-bold flex items-center justify-center">✓</span>
                            <i data-lucide="package" class="w-4 h-4 text-[#F97316] mb-1"></i>
                            <span class="text-xs font-bold text-[#172033]">{{ __('ui.roles.customer') }}</span>
                            <span class="text-[9px] text-[#718096]">{{ __('ui.auth.demo_customer_sub') }}</span>
                        </button>
                    </div>

                    <div id="demo-indicator-bar" class="hidden mt-3 p-2.5 rounded-xl bg-[#FFF7ED] border border-[#FDBA74]/60 text-[11px] text-[#172033]">
                        <span id="demo-indicator-text"></span>
                    </div>

                    <div class="text-center mt-5 pt-4 border-t border-[#E6EAF0] text-xs text-[#718096]">
                        {{ __('ui.auth.no_account') }}
                        <a href="{{ route('register') }}" class="text-[#F97316] font-bold hover:text-[#EA580C] ml-1">
                            {{ __('ui.auth.register_here') }}
                        </a>
                    </div>
                </div>

                <div class="mt-4 text-center text-[11px] text-[#718096] flex items-center justify-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#10A37F]"></i>
                    <span>{{ __('ui.auth.tls_security') }}</span>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#E6EAF0] py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#718096] gap-2">
            <div>&copy; {{ date('Y') }} CargoTrace Global Logistics. {{ __('ui.footer.all_rights') }}</div>
            <div class="flex items-center gap-3 font-medium">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10A37F]"></span>
                    <span>{{ __('ui.footer.system_operational') }}</span>
                </span>
                <span>&bull;</span>
                <a href="{{ route('tracking.public') }}" class="hover:text-[#F97316] transition">{{ __('ui.footer.public_tracking') }}</a>
            </div>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            const slash = document.getElementById('eyeSlashIcon');
            if (input.type === 'password') {
                input.type = 'text';
                eye?.classList.add('hidden');
                slash?.classList.remove('hidden');
            } else {
                input.type = 'password';
                eye?.classList.remove('hidden');
                slash?.classList.add('hidden');
            }
        }

        function fillDemoCredentials(email, password, role) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const indicatorBar = document.getElementById('demo-indicator-bar');
            const indicatorText = document.getElementById('demo-indicator-text');

            if (emailInput && passwordInput) {
                emailInput.value = email;
                passwordInput.value = password;
            }

            document.querySelectorAll('.demo-role-btn').forEach(btn => {
                btn.classList.remove('border-[#F97316]', 'bg-[#FFF7ED]', 'ring-2', 'ring-[#F97316]/15');
                btn.classList.add('border-[#E6EAF0]', 'bg-[#F4F5F9]');
                btn.querySelector('.active-badge')?.classList.add('hidden');
            });

            const activeBtn = document.getElementById('demo-' + role + '-btn');
            if (activeBtn) {
                activeBtn.classList.remove('border-[#E6EAF0]', 'bg-[#F4F5F9]');
                activeBtn.classList.add('border-[#F97316]', 'bg-[#FFF7ED]', 'ring-2', 'ring-[#F97316]/15');
                activeBtn.querySelector('.active-badge')?.classList.remove('hidden');
            }

            if (indicatorBar && indicatorText) {
                indicatorBar.classList.remove('hidden');
                const roleName = role.charAt(0).toUpperCase() + role.slice(1);
                indicatorText.innerHTML = `${@json(__('ui.auth.demo_filled_prefix'))} <strong>${roleName}</strong> (${email}) ${@json(__('ui.auth.demo_filled_suffix'))} <strong>"${@json(__('ui.auth.sign_in_btn'))}"</strong>.`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) lucide.createIcons();
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            if (emailInput && passwordInput && !emailInput.value.trim() && !passwordInput.value.trim()) {
                fillDemoCredentials('admin@cargotrace.test', 'password', 'admin');
            }
        });
    </script>
</body>
</html>
