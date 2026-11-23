<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; CargoTrace Global Logistics Platform</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#123B70',
                            800: '#123B70',
                            900: '#0E2E57',
                        },
                        logistics: {
                            DEFAULT: '#2376D9',
                            hover: '#1B5FBD',
                            light: '#EFF6FE',
                        },
                        cargo: {
                            DEFAULT: '#F59E0B',
                            hover: '#D97706',
                        },
                        surface: {
                            bg: '#F4F7FB',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            muted: '#64748B',
                        }
                    },
                    boxShadow: {
                        'soft-card': '0 10px 35px -5px rgba(18, 59, 112, 0.07), 0 4px 12px -2px rgba(18, 59, 112, 0.04)',
                        'hover-card': '0 16px 40px -6px rgba(18, 59, 112, 0.10), 0 6px 16px -3px rgba(18, 59, 112, 0.05)',
                        'real-plane': '0 24px 48px -8px rgba(18, 59, 112, 0.22), 0 12px 24px -6px rgba(18, 59, 112, 0.12)',
                        'real-box': '0 20px 36px -6px rgba(18, 59, 112, 0.18), 0 10px 18px -4px rgba(18, 59, 112, 0.10)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Radar pulse animations for international cargo hub pins */
        @keyframes pinRadar {
            0% { transform: scale(0.9); opacity: 0.85; }
            70% { transform: scale(2.4); opacity: 0; }
            100% { transform: scale(2.6); opacity: 0; }
        }
        .pin-radar-ring {
            transform-origin: center;
            animation: pinRadar 2.5s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }
        .pin-radar-ring-delayed {
            transform-origin: center;
            animation: pinRadar 2.5s cubic-bezier(0.24, 0, 0.38, 1) infinite 1.25s;
        }

        /* Subtle floating flight animation for realistic cargo aircraft */
        @keyframes planeGentleFloat {
            0%, 100% { transform: translateY(0px) rotate(-6deg); }
            50% { transform: translateY(-7px) rotate(-5.5deg); }
        }
        .animate-flight-float {
            animation: planeGentleFloat 6s ease-in-out infinite;
        }

        /* Ambient glow for live telemetry nodes */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.65; }
        }
        .animate-telemetry-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen lg:h-screen lg:overflow-hidden bg-[#F4F7FB] text-[#172B4D] antialiased flex flex-col justify-between font-sans selection:bg-[#2376D9] selection:text-white relative">

    <!-- ============================================================== -->
    <!-- 1. HEADER & LOGO CARGOTRACE (POJOK KIRI & KANAN ATAS)          -->
    <!-- ============================================================== -->
    <header class="w-full flex items-center justify-between px-6 sm:px-10 lg:px-12 pt-5 sm:pt-6 pb-2 relative z-20 shrink-0 max-w-[1520px] mx-auto">
        <!-- Logo CargoTrace (Pojok Kiri Atas) -->
        <a href="{{ url('/') }}" class="flex items-center gap-3.5 group focus:outline-none" title="CargoTrace Global Platform">
            <!-- Container / Package Icon with Navy, Logistics Blue, and Cargo Orange Accents -->
            <div class="relative w-11 h-11 rounded-xl bg-white border border-[#E2E8F0] shadow-xs flex items-center justify-center text-[#2376D9] group-hover:border-[#2376D9] group-hover:shadow-sm transition-all duration-200">
                <!-- Lucide Box / Shipping Package Icon -->
                <svg class="w-5 h-5 text-[#2376D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7.5 4.27 9 5.15"/>
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>
                <!-- Cargo Orange Seal Dot -->
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#F59E0B] ring-2 ring-white"></span>
            </div>
            
            <div class="flex flex-col">
                <span class="text-xl sm:text-[22px] font-extrabold tracking-tight leading-none">
                    <span class="text-[#123B70]">Cargo</span><span class="text-[#2376D9]">Trace</span>
                </span>
                <span class="text-[9.5px] uppercase font-bold tracking-[0.16em] text-[#64748B] mt-1">
                    GLOBAL LOGISTICS PLATFORM
                </span>
            </div>
        </a>

        <!-- Pojok Kanan Atas: Globe Icon with "Global Logistics Platform" -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-[#E2E8F0] shadow-xs text-xs font-semibold text-[#64748B]">
            <!-- Lucide Globe Icon in Logistics Blue -->
            <svg class="w-4 h-4 text-[#2376D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                <path d="M2 12h20"/>
            </svg>
            <span class="hidden sm:inline font-medium">Global Logistics Platform</span>
            <span class="sm:hidden font-medium">Global Platform</span>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- 2. MAIN LAYOUT: DUA KOLOM PROPORSIONAL (62% KIRI / 33% KANAN)  -->
    <!-- ============================================================== -->
    <main class="w-full flex-1 flex flex-col lg:flex-row items-center justify-between px-6 sm:px-10 lg:px-12 py-2 sm:py-3 lg:py-1 relative z-10 max-w-[1520px] mx-auto">
        
        <!-- ============================================================== -->
        <!-- BAGIAN KIRI (~62%): REALISTIC GLOBAL LOGISTICS & SHIPMENT VISIBILITY -->
        <!-- ============================================================== -->
        <section class="w-full lg:w-[62%] flex flex-col justify-between relative py-2 lg:py-3 pr-0 lg:pr-8 xl:pr-10">
            
            <!-- A. Tipografi & Identitas Website (Kiri Atas Menuju Tengah) -->
            <div class="relative z-20 max-w-xl mb-2 lg:mb-1">
                <!-- Label Kecil di Atas Heading -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DCEBFA]/80 border border-[#BFDBFE] text-[10.5px] font-bold text-[#123B70] mb-2.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#2376D9] animate-pulse"></span>
                    <span class="tracking-wide">FREIGHT VISIBILITY • REAL-TIME TELEMETRY</span>
                </div>

                <!-- Heading Utama (Tegas, Besar & Jelas: 42–48px) -->
                <h1 class="text-3xl sm:text-4xl lg:text-[44px] xl:text-[48px] font-extrabold text-[#123B70] tracking-tight leading-[0.98] lg:leading-[1.02]">
                    Track Your Shipment<br>
                    Across the <span class="text-[#2376D9]">World</span>
                </h1>

                <!-- Deskripsi Tepat di Bawah Heading (14–16px, #475569) -->
                <p class="text-sm sm:text-base text-[#475569] mt-3 max-w-lg leading-relaxed font-normal">
                    Lacak pengiriman Anda dengan cepat, akurat, dan real-time hanya di CargoTrace.
                </p>
            </div>

            <!-- B. Visual Utama: Realistic Global Logistics (Peta Digital, Pesawat Asli, Kardus Pengiriman) -->
            <div class="relative w-full flex-1 min-h-[340px] sm:min-h-[380px] lg:min-h-[440px] xl:min-h-[480px] flex items-center justify-center my-1 select-none">
                
                <!-- 1. Peta Dunia Digital Akurat (Memenuhi 85–95% lebar area visual kiri) -->
                <div class="relative w-[92%] sm:w-[94%] lg:w-[96%] max-w-[960px] h-auto flex items-center justify-center">
                    <img src="{{ asset('images/world-map-digital.svg') }}" 
                         alt="Peta Logistik Ekspedisi Global CargoTrace" 
                         class="w-full h-auto max-h-[400px] lg:max-h-[440px] object-contain opacity-95 pointer-events-none drop-shadow-2xs">

                    <!-- Radar Pulse Ripple Rings Overlay for Jakarta & Key Trade Hubs -->
                    <div class="absolute top-[53.5%] left-[79.7%] -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                        <span class="absolute w-8 h-8 rounded-full bg-[#F59E0B]/30 pin-radar-ring"></span>
                        <span class="absolute w-8 h-8 rounded-full bg-[#F59E0B]/20 pin-radar-ring-delayed"></span>
                    </div>
                    <div class="absolute top-[36.1%] left-[65.4%] -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                        <span class="absolute w-7 h-7 rounded-full bg-[#F59E0B]/30 pin-radar-ring"></span>
                    </div>
                </div>

                <!-- 2. Pesawat Kargo Realistis (Real Photography di Bagian Atas Peta) -->
                <div class="absolute top-[-8px] sm:top-[-12px] lg:top-[-16px] left-[34%] sm:left-[36%] lg:left-[40%] z-20 pointer-events-none animate-flight-float">
                    <div class="relative group">
                        <!-- Foto Pesawat Kargo Realistis Beresolusi Tinggi -->
                        <img src="{{ asset('images/cargo-plane-real.png') }}" 
                             alt="Pesawat Kargo Internasional CargoTrace" 
                             class="w-[260px] sm:w-[320px] lg:w-[380px] xl:w-[420px] h-auto object-contain filter drop-shadow-[0_22px_32px_rgba(18,59,112,0.24)]">
                        
                        <!-- Telemetry Badge Pesawat Kargo Asli -->
                        <div class="absolute -bottom-1 right-6 sm:right-10 flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-xs border border-[#BFDBFE] shadow-sm text-[9.5px] font-bold text-[#123B70]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2376D9] animate-ping"></span>
                            <span>CARGO FREIGHTER &bull; CT-772F IN FLIGHT</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Paket Pengiriman Realistis (Real Photography Kardus di Kiri Bawah) -->
                <div class="absolute bottom-[-10px] sm:bottom-[-14px] lg:bottom-[-18px] left-0 sm:left-2 lg:left-4 z-20 pointer-events-none">
                    <div class="relative group">
                        <!-- Foto Kardus Pengiriman Asli dengan Tekstur Kraft, Barcode & Label Ekspedisi -->
                        <img src="{{ asset('images/cardboard-boxes-real.png') }}" 
                             alt="Kardus Paket Ekspedisi & Logistik Realistis" 
                             class="w-[200px] sm:w-[250px] lg:w-[290px] xl:w-[320px] h-auto object-contain filter drop-shadow-[0_20px_30px_rgba(18,59,112,0.22)]">

                        <!-- Label Verifikasi Kargo Masuk -->
                        <div class="absolute top-3 left-4 hidden sm:flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-white/90 border border-[#E2E8F0] shadow-2xs text-[9px] font-bold text-[#123B70]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span>
                            <span>DISPATCH READY &bull; TLS ENCRYPTED</span>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- ============================================================== -->
        <!-- BAGIAN KANAN (~33%): LOGIN CARD                                -->
        <!-- ============================================================== -->
        <section class="w-full lg:w-[34%] xl:w-[33%] flex flex-col items-center justify-center py-2 lg:py-3">
            
            <!-- Login Card (Putih Murni #FFFFFF, Border #E2E8F0, Radius 16px, Soft Shadow, Lebar ~360px) -->
            <div class="w-full max-w-[360px] bg-white rounded-[16px] border border-[#E2E8F0] shadow-soft-card p-6 sm:p-7 transition-all duration-200">
                
                <!-- A. Bagian Atas Card -->
                <div class="text-center mb-5">
                    <!-- Ikon Truk Logistik dalam Kotak Navy Blue -->
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#123B70] text-white shadow-xs mb-3">
                        <!-- Lucide Truck Icon -->
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                            <path d="M15 18H9"/>
                            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                            <circle cx="17" cy="18.5" r="2.5"/>
                            <circle cx="7" cy="18.5" r="2.5"/>
                        </svg>
                    </div>

                    <!-- Judul & Deskripsi -->
                    <h2 class="text-lg sm:text-xl font-bold text-[#123B70] tracking-tight">
                        Login ke <span class="text-[#2376D9]">CargoTrace</span>
                    </h2>
                    <p class="text-xs text-[#64748B] mt-1 font-normal leading-normal">
                        Platform Pemantauan &amp; Pelacakan Ekspedisi Global
                    </p>
                </div>

                <!-- B. Notifikasi (Latar Biru Muda #EFF6FE, Border Biru Tipis #BFDBFE) -->
                @if(session('success'))
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-[#EFF6FE] border border-[#BFDBFE] p-3 text-xs text-[#123B70]">
                        <svg class="w-4 h-4 text-[#2376D9] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                        <span class="leading-relaxed font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-[#EFF6FE] border border-[#BFDBFE] p-3 text-xs text-[#123B70]">
                        <svg class="w-4 h-4 text-[#2376D9] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <span class="leading-relaxed font-medium">{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-[#EFF6FE] border border-[#BFDBFE] p-3 text-xs text-[#123B70]">
                        <svg class="w-4 h-4 text-[#F59E0B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        <span class="leading-relaxed font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-[#EFF6FE] border border-[#BFDBFE] p-3 text-xs text-[#123B70]">
                        <svg class="w-4 h-4 text-[#F59E0B] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        <span class="leading-relaxed font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- C. Form Login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                    @csrf

                    <!-- Input Alamat Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-[#172B4D] mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[#64748B]">
                                <!-- Lucide Mail Icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-9 pr-3.5 py-2.5 bg-[#F8FAFC] hover:bg-white focus:bg-white text-[#172B4D] text-xs sm:text-sm rounded-xl border border-[#E2E8F0] placeholder-[#94A3B8] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                                   placeholder="nama@perusahaan.com">
                        </div>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-[#172B4D]">
                                Kata Sandi
                            </label>
                            <span class="text-[10px] text-[#2376D9] font-semibold bg-[#EFF6FE] px-2 py-0.5 rounded-md border border-[#BFDBFE]">
                                Demo: <span class="font-mono font-bold">password</span>
                            </span>
                        </div>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[#64748B]">
                                <!-- Lucide Lock Icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                   class="w-full pl-9 pr-10 py-2.5 bg-[#F8FAFC] hover:bg-white focus:bg-white text-[#172B4D] text-xs sm:text-sm rounded-xl border border-[#E2E8F0] placeholder-[#94A3B8] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                                   placeholder="Masukkan kata sandi">
                            
                            <!-- Tombol Tampil / Sembunyikan Password -->
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#64748B] hover:text-[#123B70] focus:outline-none transition-colors" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <!-- Lucide Eye Icon -->
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <!-- Lucide Eye Off Icon -->
                                <svg id="eyeSlashIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" x2="22" y1="2" y2="22"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox Ingat Saya & Link Lacak Tanpa Login -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-[#64748B] hover:text-[#172B4D] transition-colors">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E2E8F0] text-[#2376D9] focus:ring-[#2376D9]/20 focus:ring-offset-0 transition cursor-pointer">
                            <span>Ingat Saya</span>
                        </label>
                        <a href="{{ route('tracking.public') }}" class="font-semibold text-[#2376D9] hover:text-[#123B70] hover:underline transition-colors flex items-center gap-1">
                            <span>Lacak tanpa login</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    <!-- D. Tombol Login (Logistics Blue #2376D9) -->
                    <button type="submit" class="w-full py-2.5 sm:py-3 px-4 bg-[#2376D9] hover:bg-[#1B5FBD] active:bg-[#123B70] text-white rounded-xl font-bold text-xs sm:text-sm shadow-xs hover:shadow-md transition-all duration-150 flex items-center justify-center gap-2 group cursor-pointer focus:outline-none focus:ring-4 focus:ring-[#2376D9]/20 mt-1">
                        <span>Masuk ke Akun</span>
                        <svg class="w-4 h-4 text-white group-hover:translate-x-1 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </button>
                </form>

                <!-- E. Akses Demo Penguji Cepat -->
                <div class="relative my-4 sm:my-5">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-[#E2E8F0]"></div>
                    </div>
                    <div class="relative flex justify-center text-[10px] uppercase tracking-wider text-[#64748B] font-bold">
                        <span class="bg-white px-2.5">Akses Demo Penguji Cepat</span>
                    </div>
                </div>

                <!-- 3 Tombol Demo dalam Satu Baris (Konsisten Menggunakan Lucide Icon) -->
                <div class="grid grid-cols-3 gap-2">
                    <!-- Admin Demo Button -->
                    <button type="button" 
                            onclick="fillDemoCredentials('admin@cargotrace.test', 'password', 'admin')"
                            id="demo-admin-btn"
                            title="Akun Demo: Admin (Full Access)"
                            class="demo-role-btn group relative flex flex-col items-center justify-center p-2 rounded-xl bg-[#F8FAFC] hover:bg-[#EFF6FE] border-2 border-[#E2E8F0] hover:border-[#2376D9] transition-all duration-150 text-center cursor-pointer focus:outline-none">
                        <!-- Active Indicator Badge -->
                        <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#2376D9] text-white rounded-full text-[9px] font-bold flex items-center justify-center shadow-xs">✓</span>
                        <!-- Lucide Shield Check Icon -->
                        <div class="role-icon-box w-6 h-6 rounded-lg bg-white text-[#123B70] flex items-center justify-center mb-1 shadow-2xs border border-[#E2E8F0] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-[#123B70] group-hover:text-[#2376D9] transition-colors">Admin</span>
                        <span class="text-[9px] text-[#64748B] font-medium leading-none mt-0.5">Full Access</span>
                    </button>

                    <!-- Operator Demo Button -->
                    <button type="button" 
                            onclick="fillDemoCredentials('operator@cargotrace.test', 'password', 'operator')"
                            id="demo-operator-btn"
                            title="Akun Demo: Operator (Dispatch)"
                            class="demo-role-btn group relative flex flex-col items-center justify-center p-2 rounded-xl bg-[#F8FAFC] hover:bg-[#EFF6FE] border-2 border-[#E2E8F0] hover:border-[#2376D9] transition-all duration-150 text-center cursor-pointer focus:outline-none">
                        <!-- Active Indicator Badge -->
                        <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#2376D9] text-white rounded-full text-[9px] font-bold flex items-center justify-center shadow-xs">✓</span>
                        <!-- Lucide Navigation / Route Icon -->
                        <div class="role-icon-box w-6 h-6 rounded-lg bg-white text-[#2376D9] flex items-center justify-center mb-1 shadow-2xs border border-[#E2E8F0] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="19" r="3"/>
                                <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/>
                                <circle cx="18" cy="5" r="3"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-[#123B70] group-hover:text-[#2376D9] transition-colors">Operator</span>
                        <span class="text-[9px] text-[#64748B] font-medium leading-none mt-0.5">Dispatch</span>
                    </button>

                    <!-- Customer Demo Button -->
                    <button type="button" 
                            onclick="fillDemoCredentials('customer@cargotrace.test', 'password', 'customer')"
                            id="demo-customer-btn"
                            title="Akun Demo: Customer (Shipper)"
                            class="demo-role-btn group relative flex flex-col items-center justify-center p-2 rounded-xl bg-[#F8FAFC] hover:bg-[#EFF6FE] border-2 border-[#E2E8F0] hover:border-[#F59E0B] transition-all duration-150 text-center cursor-pointer focus:outline-none">
                        <!-- Active Indicator Badge -->
                        <span class="active-badge absolute -top-1.5 -right-1.5 hidden w-4 h-4 bg-[#F59E0B] text-white rounded-full text-[9px] font-bold flex items-center justify-center shadow-xs">✓</span>
                        <!-- Lucide Package Icon -->
                        <div class="role-icon-box w-6 h-6 rounded-lg bg-white text-[#F59E0B] flex items-center justify-center mb-1 shadow-2xs border border-[#E2E8F0] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m7.5 4.27 9 5.15"/>
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5"/>
                                <path d="M12 22V12"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-[#123B70] group-hover:text-[#F59E0B] transition-colors">Customer</span>
                        <span class="text-[9px] text-[#64748B] font-medium leading-none mt-0.5">Shipper</span>
                    </button>
                </div>

                <!-- Demo Selection Feedback Banner -->
                <div id="demo-indicator-bar" class="hidden mt-2.5 p-2 rounded-xl bg-[#EFF6FE] border border-[#BFDBFE] text-xs text-[#123B70] transition-all">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#2376D9] shrink-0 animate-pulse"></span>
                        <span id="demo-indicator-text" class="leading-tight text-[10px]"></span>
                    </div>
                </div>

                <!-- F. Bagian Bawah: Link Daftar -->
                <div class="text-center mt-5 pt-3.5 border-t border-[#E2E8F0] text-xs text-[#64748B]">
                    Belum memiliki akun ekspedisi? 
                    <a href="{{ route('register') }}" class="text-[#2376D9] font-bold hover:text-[#123B70] hover:underline transition-colors ml-1">
                        Daftar di sini
                    </a>
                </div>
            </div>

            <!-- Informasi Keamanan di Bawah Kartu -->
            <div class="mt-4 text-center text-[11px] text-[#64748B] flex items-center justify-center gap-1.5 font-medium">
                <!-- Lucide Shield Check Icon -->
                <svg class="w-3.5 h-3.5 text-[#2376D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>Koneksi Aman Terenkripsi TLS 256-Bit</span>
            </div>

        </section>

    </main>

    <!-- ============================================================== -->
    <!-- 3. FOOTER SEDERHANA                                            -->
    <!-- ============================================================== -->
    <footer class="w-full px-6 sm:px-10 lg:px-12 py-3 border-t border-[#E2E8F0]/70 flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#64748B] relative z-20 shrink-0 max-w-[1520px] mx-auto">
        <div>
            &copy; {{ date('Y') }} CargoTrace Global Logistics. Seluruh hak cipta dilindungi.
        </div>
        <div class="flex items-center gap-4 mt-1 sm:mt-0 font-medium">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>System Operational 99.98%</span>
            </span>
            <span>&bull;</span>
            <a href="{{ route('tracking.public') }}" class="hover:text-[#123B70] transition-colors">Pelacakan Publik</a>
        </div>
    </footer>

    <!-- ============================================================== -->
    <!-- 4. JAVASCRIPT: PASSWORD TOGGLE & DEMO AUTO-FILL SCRIPT          -->
    <!-- ============================================================== -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        function fillDemoCredentials(email, password, role) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const indicatorBar = document.getElementById('demo-indicator-bar');
            const indicatorText = document.getElementById('demo-indicator-text');

            if (emailInput && passwordInput) {
                // Populate email and password fields directly
                emailInput.value = email;
                passwordInput.value = password;
                passwordInput.setAttribute('value', password);

                // Dispatch input/change events for reactivity
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
                emailInput.dispatchEvent(new Event('change', { bubbles: true }));
                passwordInput.dispatchEvent(new Event('change', { bubbles: true }));

                // Subtle ring pulse confirmation
                [emailInput, passwordInput].forEach(el => {
                    el.classList.add('ring-2', 'ring-[#2376D9]', 'bg-white');
                    setTimeout(() => {
                        el.classList.remove('ring-2', 'ring-[#2376D9]');
                    }, 400);
                });
            }

            // Reset active styles on all demo buttons
            const allButtons = document.querySelectorAll('.demo-role-btn');
            allButtons.forEach(btn => {
                btn.classList.remove(
                    'border-[#2376D9]', 'border-[#F59E0B]',
                    'bg-[#EFF6FE]', 'bg-[#FFFBEB]',
                    'ring-2', 'ring-[#2376D9]/20', 'ring-[#F59E0B]/20',
                    'shadow-xs'
                );
                btn.classList.add('border-[#E2E8F0]', 'bg-[#F8FAFC]');
                const badge = btn.querySelector('.active-badge');
                if (badge) {
                    badge.classList.add('hidden');
                }
            });

            // Set active state on the selected role button
            const activeBtn = document.getElementById('demo-' + role + '-btn');
            if (activeBtn) {
                activeBtn.classList.remove('border-[#E2E8F0]', 'bg-[#F8FAFC]');
                if (role === 'customer') {
                    activeBtn.classList.add('border-[#F59E0B]', 'bg-[#FFFBEB]', 'shadow-xs', 'ring-2', 'ring-[#F59E0B]/20');
                } else {
                    activeBtn.classList.add('border-[#2376D9]', 'bg-[#EFF6FE]', 'shadow-xs', 'ring-2', 'ring-[#2376D9]/20');
                }
                const badge = activeBtn.querySelector('.active-badge');
                if (badge) {
                    badge.classList.remove('hidden');
                }
            }

            // Interactive feedback banner below demo buttons
            if (indicatorBar && indicatorText) {
                indicatorBar.classList.remove('hidden');
                const roleName = role.charAt(0).toUpperCase() + role.slice(1);
                indicatorText.innerHTML = `Akun demo <strong>${roleName}</strong> (${email}) berhasil diisi. Klik tombol <strong>"Masuk ke Akun"</strong>.`;
            }
        }

        // Auto-select Admin Demo on initial load if fields are empty
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            if (emailInput && passwordInput) {
                const currentEmail = emailInput.value.trim();
                const currentPassword = passwordInput.value.trim();

                if (!currentEmail && !currentPassword) {
                    fillDemoCredentials('admin@cargotrace.test', 'password', 'admin');
                } else if (currentEmail && !currentPassword) {
                    if (currentEmail.includes('admin')) {
                        fillDemoCredentials('admin@cargotrace.test', 'password', 'admin');
                    } else if (currentEmail.includes('operator')) {
                        fillDemoCredentials('operator@cargotrace.test', 'password', 'operator');
                    } else if (currentEmail.includes('customer')) {
                        fillDemoCredentials('customer@cargotrace.test', 'password', 'customer');
                    } else {
                        passwordInput.value = 'password';
                    }
                }
            }
        });
    </script>
</body>
</html>