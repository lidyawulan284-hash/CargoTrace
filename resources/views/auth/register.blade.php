<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F5F9]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.auth.register_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-full bg-[#F4F5F9] text-[#172033] antialiased flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 font-sans">

    <div class="fixed top-4 right-4 sm:top-6 sm:right-8 z-30">
        <x-language-switcher />
    </div>

    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl border border-[#E6EAF0] shadow-[0_8px_30px_rgba(23,32,51,0.06)] p-8">
            <div class="text-center mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#172033] text-[#F97316] mb-3.5 hover:bg-[#1F2B44] transition">
                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                </a>
                <h1 class="text-xl font-extrabold tracking-tight text-[#172033]">
                    {{ __('ui.auth.register_heading_1') }} <span class="text-[#F97316]">{{ __('ui.auth.register_heading_2') }}</span>
                </h1>
                <p class="text-xs text-[#718096] mt-1">{{ __('ui.auth.register_subtitle') }}</p>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="mb-5 flex items-start gap-2.5 rounded-xl bg-red-50 border border-[#F04452]/30 p-3 text-xs text-[#F04452]">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.full_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                           placeholder="{{ __('ui.auth.full_name_placeholder') }}">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.corporate_email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                           placeholder="{{ __('ui.auth.corporate_email_placeholder') }}">
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.company_shipper') }}</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                           class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                           placeholder="{{ __('ui.auth.company_placeholder') }}">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.contact_phone') }}</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                           placeholder="+62 812 3456 7890">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.password_label') }}</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#172033] mb-1">{{ __('ui.auth.confirm_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3.5 py-2.5 bg-[#F4F5F9] focus:bg-white text-[#172033] text-sm rounded-xl border border-[#E6EAF0] focus:outline-none focus:border-[#F97316] focus:ring-2 focus:ring-[#F97316]/15 transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 bg-[#F97316] hover:bg-[#EA580C] text-white rounded-xl font-bold text-sm shadow-xs transition cursor-pointer">
                    {{ __('ui.auth.register_btn') }}
                </button>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-[#E6EAF0] text-xs text-[#718096]">
                {{ __('ui.auth.have_account') }}
                <a href="{{ route('login') }}" class="text-[#F97316] font-bold hover:text-[#EA580C] ml-1">
                    {{ __('ui.auth.sign_in_here') }}
                </a>
            </div>
        </div>

        <div class="mt-4 text-center text-[11px] text-[#718096] flex items-center justify-center gap-1.5">
            <i data-lucide="lock" class="w-3.5 h-3.5 text-[#10A37F]"></i>
            <span>{{ __('ui.auth.register_security') }}</span>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
</body>
</html>
