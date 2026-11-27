<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F5F9]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTrace') &mdash; {{ __('ui.meta.app_tagline') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Global Design Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: { DEFAULT: '#172033', 900: '#172033', 800: '#1F2B44', 700: '#2A3958' },
                        sunset: { DEFAULT: '#F97316', hover: '#EA580C', peach: '#FDBA74', soft: '#FFF7ED' },
                        surface: { bg: '#F4F5F9', card: '#FFFFFF', border: '#E6EAF0', text: '#172033', muted: '#718096' },
                        status: { success: '#10A37F', info: '#3B82F6', warning: '#F59E0B', danger: '#F04452' }
                    },
                    boxShadow: {
                        'saas': '0 1px 2px rgba(23, 32, 51, 0.04), 0 4px 12px rgba(23, 32, 51, 0.03)',
                        'saas-md': '0 4px 16px -2px rgba(23, 32, 51, 0.08), 0 2px 6px -1px rgba(23, 32, 51, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .leaflet-container { font-family: inherit; z-index: 10; border-radius: 12px; }
        [x-cloak] { display: none !important; }
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.14); border-radius: 4px; }
    </style>
</head>
<body class="h-full text-[#172033] antialiased min-h-screen bg-[#F4F5F9] font-sans flex flex-col selection:bg-[#F97316] selection:text-white overflow-x-hidden">

    <div class="flex min-h-screen w-full">

        <!-- Reusable Sidebar Component -->
        <x-sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#F4F5F9]">

            <!-- Reusable Topbar Component -->
            <x-topbar />

            <!-- Toast Flash Notifications -->
            @if(session('success') || session('warning') || session('info') || session('error') || (isset($errors) && $errors->any()))
                <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full space-y-2.5">
                    @if(session('success'))
                        <div class="bg-white border border-[#10A37F]/30 border-l-4 border-l-[#10A37F] p-3.5 rounded-xl shadow-saas flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 text-[#172033] text-xs sm:text-sm">
                                <div class="w-7 h-7 rounded-lg bg-[#10A37F]/10 text-[#10A37F] flex items-center justify-center shrink-0">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                </div>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-[#718096] hover:text-[#172033] cursor-pointer">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="bg-white border border-[#3B82F6]/30 border-l-4 border-l-[#3B82F6] p-3.5 rounded-xl shadow-saas flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 text-[#172033] text-xs sm:text-sm">
                                <div class="w-7 h-7 rounded-lg bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center shrink-0">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                </div>
                                <span class="font-medium">{{ session('info') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-[#718096] hover:text-[#172033] cursor-pointer">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-white border border-[#F59E0B]/30 border-l-4 border-l-[#F59E0B] p-3.5 rounded-xl shadow-saas flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 text-[#172033] text-xs sm:text-sm">
                                <div class="w-7 h-7 rounded-lg bg-[#F59E0B]/10 text-[#F59E0B] flex items-center justify-center shrink-0">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </div>
                                <span class="font-medium">{{ session('warning') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-[#718096] hover:text-[#172033] cursor-pointer">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error') || (isset($errors) && $errors->any()))
                        <div class="bg-white border border-[#F04452]/30 border-l-4 border-l-[#F04452] p-3.5 rounded-xl shadow-saas flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 text-[#172033] text-xs sm:text-sm">
                                <div class="w-7 h-7 rounded-lg bg-[#F04452]/10 text-[#F04452] flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    @if(session('error'))
                                        <p class="font-semibold text-[#F04452]">{{ session('error') }}</p>
                                    @endif
                                    @if(isset($errors) && $errors->any())
                                        <ul class="list-disc list-inside text-xs text-[#172033] mt-1 space-y-0.5">
                                            @foreach($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-[#718096] hover:text-[#172033] cursor-pointer">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 w-full max-w-[1440px] mx-auto">
                @hasSection('breadcrumb')
                    <nav class="flex items-center gap-1.5 text-xs text-[#718096] mb-4" aria-label="Breadcrumb">
                        <a href="{{ route('dashboard') }}" class="hover:text-[#F97316] transition font-medium flex items-center gap-1">
                            <i data-lucide="home" class="w-3.5 h-3.5"></i>
                            <span>{{ __('ui.redesign.home') }}</span>
                        </a>
                        @yield('breadcrumb')
                    </nav>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-[#E6EAF0] mt-auto py-4">
                <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#718096] gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#172033]">Cargo<span class="text-[#F97316]">Trace</span></span>
                        <span>&copy; {{ date('Y') }}</span>
                        <span class="text-[#E6EAF0]">&bull;</span>
                        <span>{{ __('ui.footer.copyright_suffix') }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        <span>{{ __('ui.footer.sla_threshold', ['hours' => config('cargotrace.delay_threshold_hours', 12)]) }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }

        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileBackdrop = document.getElementById('mobileDrawerBackdrop');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');

        function openDrawer() {
            if (mobileDrawer && mobileBackdrop) {
                mobileDrawer.classList.remove('-translate-x-full');
                mobileBackdrop.classList.remove('hidden');
            }
        }
        function closeDrawer() {
            if (mobileDrawer && mobileBackdrop) {
                mobileDrawer.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', openDrawer);
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
        if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeDrawer);

        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');

        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('userMenuContainer')?.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
