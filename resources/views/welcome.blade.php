<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7FB] text-[#172B4D] antialiased selection:bg-[#2376D9] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace &mdash; Global Shipment Visibility & Freight Tracking Platform</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#123B70',
                            900: '#0E2E57',
                            950: '#091E3B',
                        },
                        logistics: {
                            DEFAULT: '#2376D9',
                            hover: '#1B5FBD',
                            light: '#EFF6FE',
                        },
                        cargo: {
                            DEFAULT: '#F59E0B',
                            light: '#FEF3C7',
                        },
                        surface: {
                            bg: '#F4F7FB',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            dark: '#172B4D',
                            muted: '#64748B',
                        }
                    },
                    boxShadow: {
                        soft: '0 4px 20px -2px rgba(18, 59, 112, 0.06), 0 2px 6px -1px rgba(18, 59, 112, 0.04)',
                        card: '0 10px 30px -4px rgba(18, 59, 112, 0.08), 0 2px 8px -2px rgba(18, 59, 112, 0.04)',
                    }
                }
            }
        }
    </script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        html { scroll-behavior: smooth; }
        .leaflet-container { font-family: inherit; z-index: 10; border-radius: 12px; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between bg-[#F4F7FB] text-[#172B4D] font-sans antialiased">

    <!-- ================================================================= -->
    <!-- 3. HEADER DAN NAVIGATION -->
    <!-- ================================================================= -->
    <header class="bg-white border-b border-[#E2E8F0] sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Logo CargoTrace di sebelah kiri -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-11 h-11 rounded-xl bg-[#123B70] flex items-center justify-center text-white shadow-sm group-hover:bg-[#2376D9] transition-colors duration-200">
                    <!-- Ikon Truk Logistik & Kotak Paket -->
                    <svg class="w-6 h-6 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-[#123B70] leading-none">
                        Cargo<span class="text-[#2376D9]">Trace</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-[#64748B] block mt-0.5">
                        Global Logistics Visibility
                    </span>
                </div>
            </a>

            <!-- Menu Navigasi Desktop Sederhana & Rapi -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-[#172B4D]">
                <a href="{{ route('home') }}" class="text-[#2376D9] transition-colors flex items-center gap-1.5">
                    <span>Home</span>
                </a>
                <a href="#tracking" class="hover:text-[#2376D9] text-[#64748B] transition-colors">
                    Tracking
                </a>
                <a href="#services" class="hover:text-[#2376D9] text-[#64748B] transition-colors">
                    Layanan
                </a>
                <a href="#how-it-works" class="hover:text-[#2376D9] text-[#64748B] transition-colors">
                    Cara Kerja
                </a>
                <a href="#about" class="hover:text-[#2376D9] text-[#64748B] transition-colors">
                    About
                </a>
                <a href="#help" class="hover:text-[#2376D9] text-[#64748B] transition-colors">
                    Help
                </a>
            </nav>

            <!-- Bagian Kanan: Tombol Login / Autentikasi & Hamburger Mobile Toggle -->
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 bg-[#2376D9] hover:bg-[#1B5FBD] text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition duration-150">
                        <span>Dashboard Operasional</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block px-4 py-2 text-sm font-semibold text-[#123B70] hover:text-[#2376D9] transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2.5 bg-[#123B70] hover:bg-[#0E2E57] text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition duration-150">
                        Register
                    </a>
                @endauth

                <!-- Hamburger Button (Mobile Toggle) -->
                <button type="button" id="publicNavToggle" class="md:hidden p-2.5 rounded-xl border border-[#E2E8F0] text-[#123B70] hover:bg-[#F4F7FB] focus:outline-none transition-colors" aria-label="Buka menu navigasi">
                    <svg id="navOpenIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="navCloseIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <div id="publicNavMobile" class="hidden md:hidden border-t border-[#E2E8F0] bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg">
            <nav class="flex flex-col space-y-1 text-sm font-semibold text-[#172B4D]">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg bg-[#EFF6FE] text-[#2376D9]">
                    Home
                </a>
                <a href="#tracking" class="px-3 py-2 rounded-lg hover:bg-[#F4F7FB] text-[#64748B] hover:text-[#2376D9]">
                    Tracking
                </a>
                <a href="#services" class="px-3 py-2 rounded-lg hover:bg-[#F4F7FB] text-[#64748B] hover:text-[#2376D9]">
                    Layanan
                </a>
                <a href="#how-it-works" class="px-3 py-2 rounded-lg hover:bg-[#F4F7FB] text-[#64748B] hover:text-[#2376D9]">
                    Cara Kerja
                </a>
                <a href="#about" class="px-3 py-2 rounded-lg hover:bg-[#F4F7FB] text-[#64748B] hover:text-[#2376D9]">
                    About
                </a>
                <a href="#help" class="px-3 py-2 rounded-lg hover:bg-[#F4F7FB] text-[#64748B] hover:text-[#2376D9]">
                    Help
                </a>
            </nav>

            <div class="pt-3 border-t border-[#E2E8F0] flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center py-2.5 px-4 bg-[#2376D9] text-white text-sm font-semibold rounded-xl">
                        Buka Dashboard Operasional
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 px-4 bg-[#F4F7FB] border border-[#E2E8F0] text-[#123B70] text-sm font-semibold rounded-xl hover:bg-[#EFF6FE]">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2.5 px-4 bg-[#123B70] text-white text-sm font-semibold rounded-xl hover:bg-[#0E2E57]">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ================================================================= -->
    <!-- 4. HERO SECTION (LAYOUT 2 KOLOM) -->
    <!-- ================================================================= -->
    <section class="relative pt-10 pb-16 lg:pt-16 lg:pb-24 overflow-hidden border-b border-[#E2E8F0] bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- BAGIAN KIRI: Form Pelacakan & Informasi Utama -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Label Kecil: TRACK • SHIP • DELIVER -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#EFF6FE] border border-[#BFDBFE] text-xs font-bold tracking-widest text-[#2376D9] uppercase">
                        <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                        TRACK &bull; SHIP &bull; DELIVER
                    </div>

                    <!-- Heading Besar -->
                    <h1 class="text-3xl sm:text-4xl lg:text-[50px] font-extrabold text-[#123B70] leading-[1.18] tracking-tight">
                        Shipments Made <span class="text-[#2376D9]">Easier</span>, <br class="hidden sm:inline"/>
                        Worldwide.
                    </h1>

                    <!-- Deskripsi -->
                    <p class="text-base sm:text-lg text-[#64748B] max-w-xl leading-relaxed">
                        Lacak pengiriman Anda dengan cepat, akurat, dan real-time hanya di CargoTrace. Pantau setiap pergerakan kontainer udara, laut, dan darat dalam satu platform terpadu.
                    </p>

                    <!-- Kolom Pencarian Nomor Resi yang Cukup Besar -->
                    <div id="tracking" class="pt-2">
                        <form method="GET" action="{{ route('tracking.public') }}" class="relative">
                            <div class="bg-[#F4F7FB] p-2 rounded-2xl border-2 border-[#E2E8F0] hover:border-[#BFDBFE] focus-within:border-[#2376D9] focus-within:ring-4 focus-within:ring-[#2376D9]/10 transition-all shadow-sm">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                    <div class="flex items-center flex-1 px-3 py-2">
                                        <!-- Lucide Search Icon -->
                                        <svg class="w-5 h-5 text-[#2376D9] shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text" name="tracking_number" value="{{ $trackingNumber }}" required
                                               placeholder="Masukkan Nomor Resi (Contoh: CT-2026-SEA-88)" 
                                               class="w-full bg-transparent text-sm sm:text-base font-semibold text-[#172B4D] placeholder-[#64748B] focus:outline-none uppercase tracking-wide">
                                    </div>
                                    <!-- Tombol Utama: Lacak Paket -->
                                    <button type="submit" class="sm:shrink-0 px-6 py-3.5 bg-[#2376D9] hover:bg-[#1B5FBD] active:bg-[#164CA0] text-white text-sm font-bold rounded-xl shadow-sm transition duration-150 flex items-center justify-center gap-2 cursor-pointer">
                                        <span>Lacak Paket</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Contoh Nomor Resi di Bawah Kolom Pencarian -->
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-[#64748B] font-medium">Contoh nomor resi aktif:</span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @forelse($sampleShipments as $samp)
                                    <a href="{{ route('tracking.public', ['tracking_number' => $samp->shipment_number]) }}" 
                                       class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#EFF6FE] hover:bg-[#DBEAFE] border border-[#BFDBFE] text-xs font-mono font-semibold text-[#2376D9] hover:text-[#123B70] transition-colors">
                                        {{ $samp->shipment_number }}
                                    </a>
                                @empty
                                    <span class="text-xs font-mono text-[#64748B]">CT-2026-SEA-8841</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Demo 1-Click Grading & Evaluation Ribbon (Sistem UAS Preserved) -->
                        <div class="mt-6 pt-5 border-t border-[#E2E8F0]">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#64748B] flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                    Akses Cepat Penguji &amp; Demo Akun (Bypass Login)
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-2.5">
                                <a href="{{ route('demo.login', 'admin') }}" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white hover:bg-[#F0F5FA] border border-[#E2E8F0] hover:border-[#2376D9] rounded-xl text-xs font-semibold text-[#123B70] transition-all shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-[#123B70]"></span>
                                    <span>Admin</span>
                                </a>
                                <a href="{{ route('demo.login', 'operator') }}" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white hover:bg-[#F0F5FA] border border-[#E2E8F0] hover:border-[#2376D9] rounded-xl text-xs font-semibold text-[#2376D9] transition-all shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-[#2376D9]"></span>
                                    <span>Operator</span>
                                </a>
                                <a href="{{ route('demo.login', 'customer') }}" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white hover:bg-[#F0F5FA] border border-[#E2E8F0] hover:border-[#F59E0B] rounded-xl text-xs font-semibold text-[#172B4D] transition-all shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                    <span>Customer</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- BAGIAN KANAN: Ilustrasi Realistis & Profesional Ekspedisi Logistik -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto w-full max-w-[480px] bg-gradient-to-br from-[#F4F7FB] to-white rounded-3xl border border-[#E2E8F0] p-6 shadow-card overflow-hidden">
                        
                        <!-- Floating Live Status Badge di Pojok Kanan Atas -->
                        <div class="flex items-center justify-between pb-4 border-b border-[#E2E8F0] mb-4">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F59E0B] opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#F59E0B]"></span>
                                </span>
                                <span class="text-xs font-bold text-[#123B70] uppercase tracking-wider">Live Fleet Telematics</span>
                            </div>
                            <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded bg-[#EFF6FE] text-[#2376D9] border border-[#BFDBFE]">
                                MULTIMODAL HUB
                            </span>
                        </div>

                        <!-- SVG Hero Graphic: Pesawat Kargo, Truk Ekspedisi CargoTrace, Paket & Peta Rute -->
                        <div class="relative w-full h-[320px] flex items-center justify-center">
                            <svg class="w-full h-full" viewBox="0 0 460 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="routeGrad" x1="40" y1="240" x2="420" y2="70" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#2376D9"/>
                                        <stop offset="70%" stop-color="#F59E0B"/>
                                    </linearGradient>
                                    <filter id="softGlow" x="-10%" y="-10%" width="120%" height="120%">
                                        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#123B70" flood-opacity="0.12"/>
                                    </filter>
                                </defs>

                                <!-- Peta Kontinental Stylized (World Map Background) -->
                                <g opacity="0.25">
                                    <circle cx="80" cy="90" r="3" fill="#64748B"/>
                                    <circle cx="120" cy="70" r="4" fill="#64748B"/>
                                    <circle cx="150" cy="100" r="3" fill="#64748B"/>
                                    <circle cx="210" cy="80" r="5" fill="#64748B"/>
                                    <circle cx="270" cy="95" r="4" fill="#64748B"/>
                                    <circle cx="340" cy="75" r="5" fill="#64748B"/>
                                    <circle cx="390" cy="110" r="4" fill="#64748B"/>
                                    <circle cx="70" cy="180" r="4" fill="#64748B"/>
                                    <circle cx="130" cy="210" r="5" fill="#64748B"/>
                                    <circle cx="220" cy="190" r="3" fill="#64748B"/>
                                    <circle cx="300" cy="220" r="4" fill="#64748B"/>
                                    <circle cx="380" cy="180" r="5" fill="#64748B"/>
                                    <!-- Garis-garis lintang & bujur halus -->
                                    <path d="M20 100 C150 120, 300 80, 440 100" stroke="#CBD5E1" stroke-dasharray="3 4"/>
                                    <path d="M20 180 C150 160, 300 200, 440 180" stroke="#CBD5E1" stroke-dasharray="3 4"/>
                                </g>

                                <!-- Garis Rute Pengiriman Global (Arced Flight/Maritime Line) -->
                                <path d="M 60 220 C 130 110, 260 70, 390 100" stroke="url(#routeGrad)" stroke-width="3" stroke-dasharray="6 6"/>

                                <!-- Checkpoint Nodes & Pins -->
                                <circle cx="60" cy="220" r="6" fill="#123B70" stroke="#FFFFFF" stroke-width="2"/>
                                <circle cx="210" cy="108" r="5" fill="#2376D9" stroke="#FFFFFF" stroke-width="2"/>
                                <circle cx="390" cy="100" r="7" fill="#F59E0B" stroke="#FFFFFF" stroke-width="2.5"/>

                                <!-- Pesawat Kargo CargoTrace (Air Freight Vector) -->
                                <g transform="translate(235, 70) rotate(-14)" filter="url(#softGlow)">
                                    <!-- Fuselage -->
                                    <ellipse cx="60" cy="20" rx="42" ry="10" fill="#FFFFFF" stroke="#123B70" stroke-width="2"/>
                                    <!-- Cockpit Window -->
                                    <path d="M90 18 Q96 20 92 23 Z" fill="#123B70"/>
                                    <!-- Tail Fin (Vertical Stabilizer) with Orange Accent -->
                                    <path d="M24 16 L12 0 L22 0 L34 16 Z" fill="#123B70"/>
                                    <path d="M14 2 L19 2 L23 8 L18 8 Z" fill="#F59E0B"/>
                                    <!-- Wing (Main Wing) -->
                                    <polygon points="46,20 62,48 76,48 64,20" fill="#2376D9"/>
                                    <!-- Jet Engine -->
                                    <rect x="52" y="32" width="16" height="6" rx="3" fill="#123B70"/>
                                    <!-- Brand text on plane body -->
                                    <text x="36" y="23" font-size="6.5" font-weight="900" fill="#123B70" letter-spacing="0.5">CARGOTRACE</text>
                                </g>

                                <!-- Truk Ekspedisi CargoTrace (Logistics Delivery Truck) -->
                                <g transform="translate(40, 195)" filter="url(#softGlow)">
                                    <!-- Ground Shadow -->
                                    <ellipse cx="100" cy="72" rx="95" ry="8" fill="#172B4D" opacity="0.1"/>
                                    
                                    <!-- Container Box Trailer -->
                                    <rect x="15" y="10" width="115" height="52" rx="4" fill="#FFFFFF" stroke="#123B70" stroke-width="2.5"/>
                                    <!-- Container Corrugated Panels -->
                                    <line x1="32" y1="12" x2="32" y2="60" stroke="#E2E8F0" stroke-width="2"/>
                                    <line x1="50" y1="12" x2="50" y2="60" stroke="#E2E8F0" stroke-width="2"/>
                                    <line x1="68" y1="12" x2="68" y2="60" stroke="#E2E8F0" stroke-width="2"/>
                                    <line x1="86" y1="12" x2="86" y2="60" stroke="#E2E8F0" stroke-width="2"/>
                                    <line x1="104" y1="12" x2="104" y2="60" stroke="#E2E8F0" stroke-width="2"/>
                                    
                                    <!-- Logo on Trailer with Orange Accent Stripe -->
                                    <rect x="15" y="44" width="115" height="4" fill="#F59E0B"/>
                                    <rect x="25" y="24" width="18" height="18" rx="4" fill="#123B70"/>
                                    <path d="M30 33 L34 37 L40 28" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
                                    <text x="48" y="37" font-size="11" font-weight="900" fill="#123B70" letter-spacing="0.5">CargoTrace</text>

                                    <!-- Truck Cab (Cabin Depan) -->
                                    <path d="M130 25 L155 25 Q168 25 174 38 L180 50 L180 62 L130 62 Z" fill="#123B70"/>
                                    <!-- Windshield -->
                                    <path d="M148 29 L162 29 Q168 30 171 40 L152 40 Z" fill="#EFF6FE"/>
                                    <!-- Front Bumper & Headlight -->
                                    <rect x="175" y="52" width="7" height="6" rx="1.5" fill="#F59E0B"/>
                                    <rect x="170" y="58" width="12" height="4" fill="#334155"/>

                                    <!-- Wheels -->
                                    <circle cx="42" cy="64" r="11" fill="#172B4D" stroke="#94A3B8" stroke-width="2.5"/>
                                    <circle cx="42" cy="64" r="4.5" fill="#CBD5E1"/>
                                    <circle cx="95" cy="64" r="11" fill="#172B4D" stroke="#94A3B8" stroke-width="2.5"/>
                                    <circle cx="95" cy="64" r="4.5" fill="#CBD5E1"/>
                                    <circle cx="160" cy="64" r="11" fill="#172B4D" stroke="#94A3B8" stroke-width="2.5"/>
                                    <circle cx="160" cy="64" r="4.5" fill="#CBD5E1"/>
                                </g>

                                <!-- Kotak Paket Pengiriman (Isometric Parcels) -->
                                <g transform="translate(260, 205)" filter="url(#softGlow)">
                                    <!-- Parcel 1: Large Box -->
                                    <rect x="15" y="15" width="46" height="40" rx="3" fill="#FEF3C7" stroke="#D97706" stroke-width="1.8"/>
                                    <line x1="38" y1="15" x2="38" y2="55" stroke="#F59E0B" stroke-width="5"/>
                                    <!-- Barcode Sticker -->
                                    <rect x="20" y="24" width="14" height="12" fill="#FFFFFF" stroke="#E2E8F0"/>
                                    <line x1="23" y1="27" x2="23" y2="33" stroke="#123B70" stroke-width="1.5"/>
                                    <line x1="26" y1="27" x2="26" y2="33" stroke="#123B70" stroke-width="1"/>
                                    <line x1="29" y1="27" x2="29" y2="33" stroke="#123B70" stroke-width="2"/>
                                    <line x1="32" y1="27" x2="32" y2="33" stroke="#123B70" stroke-width="1"/>

                                    <!-- Parcel 2: Small Box Stacking -->
                                    <rect x="68" y="26" width="34" height="29" rx="3" fill="#EFF6FE" stroke="#2376D9" stroke-width="1.8"/>
                                    <line x1="85" y1="26" x2="85" y2="55" stroke="#2376D9" stroke-width="4"/>
                                    <rect x="72" y="32" width="10" height="8" fill="#FFFFFF"/>
                                    <!-- Fragile Stamp Accent -->
                                    <circle cx="95" cy="38" r="4" fill="#F59E0B"/>
                                </g>
                            </svg>
                        </div>

                        <!-- Floating Route Metrics Strip -->
                        <div class="mt-4 pt-3 border-t border-[#E2E8F0] grid grid-cols-3 gap-2 text-center">
                            <div class="p-2 rounded-xl bg-white border border-[#E2E8F0]">
                                <span class="text-[10px] uppercase font-bold text-[#64748B] block">Origin Hub</span>
                                <span class="text-xs font-bold text-[#123B70]">SIN &bull; Port</span>
                            </div>
                            <div class="p-2 rounded-xl bg-white border border-[#E2E8F0]">
                                <span class="text-[10px] uppercase font-bold text-[#F59E0B] block">Transit Telemetry</span>
                                <span class="text-xs font-bold text-[#2376D9]">In Flight &bull; ETA On Time</span>
                            </div>
                            <div class="p-2 rounded-xl bg-white border border-[#E2E8F0]">
                                <span class="text-[10px] uppercase font-bold text-[#64748B] block">Destination</span>
                                <span class="text-xs font-bold text-[#123B70]">JKT &bull; Hub</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- HASIL PELACAKAN RESI AKTIF (JIKA DILACAK) -->
    <!-- ================================================================= -->
    @if($shipment)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-10 mb-16 relative z-20">
            <div class="bg-white rounded-2xl border-2 border-[#BFDBFE] p-6 sm:p-8 shadow-card space-y-6">
                
                <!-- Shipment Header Ribbon -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E2E8F0] pb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-[#123B70] text-white">
                                KONSINYASI AKTIF
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#123B70] font-mono tracking-tight">{{ $shipment->shipment_number }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-[#EFF6FE] text-[#2376D9] border border-[#BFDBFE] tracking-wider">
                                {{ $shipment->status }}
                            </span>
                            @if($shipment->is_delayed)
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-[#FEF3C7] text-[#B45309] border border-[#FDE68A] tracking-wider flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    TERLAMBAT +{{ $shipment->delay_hours }}H ({{ $shipment->delay_severity }})
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 tracking-wider">
                                    ✓ SESUAI JADWAL (ON SCHEDULE)
                                </span>
                            @endif
                        </div>
                        <div class="text-xs sm:text-sm text-[#64748B] mt-2 flex flex-wrap items-center gap-2">
                            <span>Ekspedisi: <strong class="text-[#123B70] font-bold">{{ $shipment->carrier->name }}</strong></span>
                            <span class="text-[#CBD5E1]">&bull;</span>
                            <span>Rute Pengiriman: <strong class="text-[#123B70]">{{ $shipment->origin->city }}</strong> &rarr; <strong class="text-[#123B70]">{{ $shipment->destination->city }}</strong></span>
                        </div>
                    </div>

                    <div class="sm:text-right text-xs bg-[#F4F7FB] p-3 rounded-xl border border-[#E2E8F0]">
                        <span class="text-[#64748B] block text-[10px] uppercase font-bold tracking-wider mb-1">Estimasi Tiba (ETA)</span>
                        <span class="text-[#123B70] font-bold block text-sm">{{ $shipment->expected_arrival->format('M d, Y') }}</span>
                        @if($shipment->is_delayed)
                            <span class="text-[#F59E0B] text-xs font-bold mt-0.5 block">Penyesuaian: {{ $shipment->estimated_arrival?->format('M d, Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <!-- GIS Map & Weather Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="text-xs font-bold text-[#123B70] mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#2376D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Rute Jalur GIS Real-Time (Leaflet.js + OSM)
                            </span>
                            <span class="text-[11px] text-[#2376D9] font-mono font-bold">{{ count($mapPoints) }} titik singgah terpantau</span>
                        </div>
                        <div id="publicMap" class="w-full h-80 sm:h-96 rounded-xl border border-[#E2E8F0] shadow-inner"></div>
                    </div>

                    <!-- Telematika Cuaca (Open-Meteo REST service) -->
                    <div class="bg-[#F4F7FB] p-5 sm:p-6 rounded-xl border border-[#E2E8F0] flex flex-col justify-between text-xs">
                        <div>
                            <div class="flex items-center justify-between pb-3.5 border-b border-[#E2E8F0]">
                                <div>
                                    <h4 class="font-bold text-[#123B70] text-sm">Telematika Cuaca Rute</h4>
                                    <span class="text-[10px] text-[#64748B]">Open-Meteo REST API Telemetry</span>
                                </div>
                                <span class="text-2xl">{{ $weather['icon'] ?? '🌤️' }}</span>
                            </div>

                            @if($weather)
                                <div class="mt-4 space-y-3">
                                    <div class="bg-white p-3 rounded-lg border border-[#E2E8F0]">
                                        <span class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider">Lokasi Stasiun Cuaca</span>
                                        <div class="font-bold text-[#123B70] text-sm mt-0.5">{{ $weather['location'] }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-center">
                                        <div class="bg-white p-3 rounded-lg border border-[#E2E8F0]">
                                            <span class="text-[10px] text-[#64748B] uppercase font-bold block">Suhu Udara</span>
                                            <span class="text-base font-extrabold text-[#2376D9] mt-1 block">{{ $weather['temperature'] }}{{ $weather['temperature_unit'] }}</span>
                                        </div>
                                        <div class="bg-white p-3 rounded-lg border border-[#E2E8F0]">
                                            <span class="text-[10px] text-[#64748B] uppercase font-bold block">Kecepatan Angin</span>
                                            <span class="text-base font-extrabold text-[#123B70] mt-1 block">{{ $weather['wind_speed'] }} <span class="text-xs font-normal text-[#64748B]">{{ $weather['wind_speed_unit'] }}</span></span>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-lg bg-white text-[#172B4D] text-xs border border-[#E2E8F0]">
                                        Kondisi Atmosfer: <strong class="text-[#123B70] font-semibold">{{ $weather['condition'] }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-[10px] text-[#64748B] pt-3.5 border-t border-[#E2E8F0] text-center">
                            Data cuaca tersinkronisasi otomatis dengan koordinat armada
                        </div>
                    </div>
                </div>

                <!-- Riwayat Perjalanan Checkpoint -->
                <div class="pt-4 border-t border-[#E2E8F0]">
                    <h3 class="text-sm font-bold text-[#123B70] mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Milestone Riwayat Perjalanan Kargo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        @foreach($shipment->trackingEvents as $evt)
                            <div class="bg-[#F4F7FB] p-4 rounded-xl border border-[#E2E8F0] flex flex-col justify-between hover:border-[#2376D9] transition">
                                <div>
                                    <div class="flex items-center justify-between font-bold text-[#123B70] mb-1.5">
                                        <span class="truncate pr-2">{{ $evt->location_name }}</span>
                                        <span class="text-[9px] uppercase px-2 py-0.5 rounded bg-white text-[#2376D9] border border-[#BFDBFE] font-mono shrink-0">{{ $evt->status }}</span>
                                    </div>
                                    <p class="text-[#64748B] mt-1 line-clamp-2 leading-relaxed text-[11px]">{{ $evt->description }}</p>
                                </div>
                                <div class="text-[10px] text-[#64748B] font-mono mt-3 pt-2.5 border-t border-[#E2E8F0]">
                                    {{ $evt->event_time->format('d M Y, H:i') }} WIB
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>
    @elseif(!empty($trackingNumber))
        <section class="max-w-xl mx-auto px-4 py-8">
            <div class="bg-white border-l-4 border-[#F59E0B] rounded-xl p-5 shadow-sm border border-[#E2E8F0] flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-[#FEF3C7] text-[#B45309] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#123B70]">Nomor Resi Tidak Ditemukan</h3>
                    <p class="text-xs text-[#64748B] mt-1 leading-relaxed">
                        Nomor resi <strong class="font-mono text-[#172B4D]">{{ $trackingNumber }}</strong> tidak tercatat dalam sistem kami. Pastikan nomor resi yang Anda masukkan sudah sesuai dengan format CargoTrace.
                    </p>
                </div>
            </div>
        </section>
    @endif

    <!-- ================================================================= -->
    <!-- 5. SERVICE FEATURES (EMPAT INFORMASI LAYANAN DALAM SATU BARIS) -->
    <!-- ================================================================= -->
    <section id="services" class="py-16 bg-[#F4F7FB]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2376D9] block mb-2">Keunggulan Ekspedisi</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#123B70] tracking-tight">
                    Layanan Logistik Standar Internasional
                </h2>
                <p class="text-sm text-[#64748B] mt-2">
                    Infrastruktur terintegrasi yang dirancang untuk keandalan dan kecepatan pengiriman kargo Anda.
                </p>
            </div>

            <!-- Grid 4 Kartu Horizontal (White, Border Tipis, Shadow Lembut, Ikon Lucide) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Layanan 1: Real-Time Tracking -->
                <div class="bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-soft hover:border-[#2376D9] hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Lucide Activity / Radar Icon -->
                        <div class="w-12 h-12 rounded-xl bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#123B70] mb-2">Real-Time Tracking</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Pantau status pengiriman secara langsung dengan pembaruan rute telematika dan koordinat GPS presisi tinggi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center text-xs font-semibold text-[#2376D9]">
                        <span>Akurasi Telemetri</span>
                    </div>
                </div>

                <!-- Layanan 2: Aman & Terpercaya -->
                <div class="bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-soft hover:border-[#2376D9] hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Lucide ShieldCheck Icon -->
                        <div class="w-12 h-12 rounded-xl bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#123B70] mb-2">Aman &amp; Terpercaya</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Data pengiriman terlindungi dengan baik menggunakan sistem enkripsi ketat dan verifikasi keamanan bertingkat.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center text-xs font-semibold text-[#2376D9]">
                        <span>Audit Log Terproteksi</span>
                    </div>
                </div>

                <!-- Layanan 3: Jangkauan Global -->
                <div class="bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-soft hover:border-[#2376D9] hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Lucide Globe Icon -->
                        <div class="w-12 h-12 rounded-xl bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#123B70] mb-2">Jangkauan Global</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Kirim barang ke berbagai wilayah dengan jaringan rute ekspedisi multimoda udara, laut, dan armada darat.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center text-xs font-semibold text-[#2376D9]">
                        <span>Multi-Hub Interkoneksi</span>
                    </div>
                </div>

                <!-- Layanan 4: Customer Support -->
                <div class="bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-soft hover:border-[#2376D9] hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Lucide Headphones Icon -->
                        <div class="w-12 h-12 rounded-xl bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#123B70] mb-2">Customer Support</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Tim bantuan operasional ekspedisi siap membantu Anda mengatasi kendala pengiriman dan pembaruan dokumen.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center text-xs font-semibold text-[#2376D9]">
                        <span>Siap Membantu Pengguna</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- 6. HOW IT WORKS (CARA KERJA CARGOTRACE) -->
    <!-- ================================================================= -->
    <section id="how-it-works" class="py-16 bg-white border-y border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#2376D9] block mb-2">Alur Pengiriman</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#123B70] tracking-tight">
                    Cara Kerja CargoTrace
                </h2>
                <p class="text-sm text-[#64748B] mt-2">
                    Lacak konsinyasi dan pengiriman ekspedisi Anda hanya dalam tiga langkah mudah dan terstruktur.
                </p>
            </div>

            <!-- Desain Step-by-Step dengan Garis Penghubung -->
            <div class="relative">
                
                <!-- Garis Penghubung Horizontal (Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-[15%] right-[15%] h-0.5 border-t-2 border-dashed border-[#BFDBFE] -translate-y-8 z-0"></div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
                    
                    <!-- Langkah 1: Masukkan Nomor Resi -->
                    <div class="bg-[#F4F7FB] p-8 rounded-2xl border border-[#E2E8F0] text-center hover:shadow-soft transition-all">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-[#2376D9] text-[#2376D9] mx-auto flex items-center justify-center mb-6 shadow-sm relative">
                            <!-- Lucide Package Box Icon -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full bg-[#123B70] text-white text-xs font-extrabold flex items-center justify-center">1</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#123B70] mb-2">1. Masukkan Nomor Resi</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Input nomor resi atau referensi konsinyasi CargoTrace Anda pada form pelacakan di atas.
                        </p>
                    </div>

                    <!-- Langkah 2: Lacak Pengiriman -->
                    <div class="bg-[#F4F7FB] p-8 rounded-2xl border border-[#E2E8F0] text-center hover:shadow-soft transition-all">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-[#F59E0B] text-[#F59E0B] mx-auto flex items-center justify-center mb-6 shadow-sm relative">
                            <!-- Lucide Search with Compass Icon -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full bg-[#F59E0B] text-white text-xs font-extrabold flex items-center justify-center">2</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#123B70] mb-2">2. Lacak Pengiriman</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Lihat posisi armada, rute peta GIS, status pos singgah, cuaca, serta estimasi waktu ketibaan secara langsung.
                        </p>
                    </div>

                    <!-- Langkah 3: Terima Barang -->
                    <div class="bg-[#F4F7FB] p-8 rounded-2xl border border-[#E2E8F0] text-center hover:shadow-soft transition-all">
                        <div class="w-16 h-16 rounded-2xl bg-white border-2 border-emerald-600 text-emerald-600 mx-auto flex items-center justify-center mb-6 shadow-sm relative">
                            <!-- Lucide CheckCircle Icon -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full bg-emerald-700 text-white text-xs font-extrabold flex items-center justify-center">3</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#123B70] mb-2">3. Terima Barang</h3>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Paket kargo tiba tepat waktu di lokasi tujuan dengan konfirmasi serah terima aman dan bukti manifest digital.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ================================================================= -->
    <!-- ABOUT SECTION & LOGISTICS SPECIFICATIONS -->
    <!-- ================================================================= -->
    <section id="about" class="py-16 bg-[#F4F7FB]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-[#E2E8F0] p-8 sm:p-12 shadow-soft">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#2376D9] block mb-2">Tentang Platform</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#123B70] tracking-tight leading-snug">
                            Solusi Visibilitas Rantai Pasok Terdepan untuk Dunia Logistik Modern
                        </h2>
                        <p class="text-sm text-[#64748B] mt-4 leading-relaxed">
                            CargoTrace dibangun untuk menjembatani transparansi kargo antar negara. Dengan implementasi mesin status (state machine) deterministik, penanganan deviasi rute otomatis, dan komputasi estimasi kedatangan (ETA) berbasis kondisi riil di lapangan.
                        </p>
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="p-4 rounded-xl bg-[#F4F7FB] border border-[#E2E8F0]">
                                <span class="text-2xl font-extrabold text-[#123B70]">99.8%</span>
                                <span class="text-xs text-[#64748B] block mt-0.5">Akurasi Status Transit</span>
                            </div>
                            <div class="p-4 rounded-xl bg-[#F4F7FB] border border-[#E2E8F0]">
                                <span class="text-2xl font-extrabold text-[#2376D9]">24/7</span>
                                <span class="text-xs text-[#64748B] block mt-0.5">Pemantauan Telematika GIS</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#F4F7FB] p-6 rounded-xl border border-[#E2E8F0] space-y-4">
                        <h4 class="text-sm font-bold text-[#123B70] uppercase tracking-wide">Pilar Kepatuhan Operasional</h4>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
                            <div class="text-xs text-[#64748B]">
                                <strong class="text-[#123B70]">Deterministic State Machine:</strong> Mencegah regresi status ilegal selama siklus hidup pengiriman.
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
                            <div class="text-xs text-[#64748B]">
                                <strong class="text-[#123B70]">GIS &amp; Open-Meteo Integration:</strong> Pemetaan koordinat real-time dengan pemantauan anomali cuaca.
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#EFF6FE] text-[#2376D9] flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
                            <div class="text-xs text-[#64748B]">
                                <strong class="text-[#123B70]">Role-Based Access Control:</strong> Hak akses tersegregasi antara Administrator, Operator, dan Pelanggan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- HELP & SUPPORT SECTION -->
    <!-- ================================================================= -->
    <section id="help" class="py-12 bg-white border-t border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-6 sm:p-8 rounded-2xl bg-[#123B70] text-white">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#F59E0B]">Pusat Bantuan Logistik</span>
                    <h3 class="text-xl sm:text-2xl font-bold">Membutuhkan Bantuan Khusus Pengiriman?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                        Tim Customer Dispatch CargoTrace siap membantu pengecekan status konsinyasi, dokumen manifest, dan rute transit kargo Anda.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('login') }}" class="px-5 py-3 rounded-xl bg-[#2376D9] hover:bg-[#1B5FBD] text-white text-xs sm:text-sm font-bold shadow-sm transition">
                        Hubungi Support
                    </a>
                    <a href="#tracking" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-bold transition">
                        Cek Resi Lagi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- FOOTER -->
    <!-- ================================================================= -->
    <footer class="bg-white border-t border-[#E2E8F0] py-10 text-xs text-[#64748B]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-[#123B70] flex items-center justify-center text-white">
                        <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-[#123B70]">CargoTrace</span> &mdash; Global Shipment Visibility Platform
                    </div>
                </div>
                <div class="flex items-center space-x-6 text-[#64748B]">
                    <a href="#services" class="hover:text-[#2376D9]">Layanan</a>
                    <a href="#how-it-works" class="hover:text-[#2376D9]">Cara Kerja</a>
                    <a href="#about" class="hover:text-[#2376D9]">Tentang Kami</a>
                    <a href="#help" class="hover:text-[#2376D9]">Pusat Bantuan</a>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#64748B] gap-2">
                <span>&copy; {{ date('Y') }} CargoTrace. Pemrograman Web 2 UAS Portfolio Final Project. All rights reserved.</span>
                <span>Standar Mutu Logistik Internasional &bull; MariaDB &bull; Laravel 12 &bull; GIS OpenStreetMap</span>
            </div>
        </div>
    </footer>

    <!-- LEAFLET MAP INITIALIZATION SCRIPT (FUNGSI ASLI PRESERVED) -->
    @if($shipment && !empty($mapPoints))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const points = @json($mapPoints);
        if (points.length === 0) return;

        const map = L.map('publicMap').setView([points[0].lat, points[0].lng], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const latLngs = [];
        points.forEach((pt) => {
            latLngs.push([pt.lat, pt.lng]);
            const marker = L.marker([pt.lat, pt.lng]).addTo(map);
            marker.bindPopup(`<strong>${pt.name}</strong><br/>${pt.city}<br/><span style="color:#2376D9;font-weight:bold;">${pt.time}</span>`);
        });

        if (latLngs.length > 1) {
            const polyline = L.polyline(latLngs, { color: '#2376D9', weight: 3.5, dashArray: '6, 8' }).addTo(map);
            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        }
    });
    </script>
    @endif

    <!-- MOBILE NAV TOGGLE SCRIPT -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('publicNavToggle');
        const mobileMenu = document.getElementById('publicNavMobile');
        const openIcon = document.getElementById('navOpenIcon');
        const closeIcon = document.getElementById('navCloseIcon');

        if (toggleBtn && mobileMenu) {
            toggleBtn.addEventListener('click', function () {
                const isHidden = mobileMenu.classList.contains('hidden');
                if (isHidden) {
                    mobileMenu.classList.remove('hidden');
                    openIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                } else {
                    mobileMenu.classList.add('hidden');
                    openIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });

            // Auto close mobile menu when clicking internal anchor links
            mobileMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    mobileMenu.classList.add('hidden');
                    openIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                });
            });
        }
    });
    </script>

</body>
</html>
