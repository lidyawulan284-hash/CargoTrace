<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Pelanggan &mdash; CargoTrace Platform</title>

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
                        },
                        logistics: {
                            DEFAULT: '#2376D9',
                            hover: '#1B5FBD',
                            light: '#EFF6FE',
                        },
                        cargo: {
                            DEFAULT: '#F59E0B',
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
                        card: '0 8px 30px -4px rgba(18, 59, 112, 0.08), 0 2px 6px -1px rgba(18, 59, 112, 0.04)',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full bg-[#F4F7FB] text-[#172B4D] antialiased flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">

    <div class="w-full max-w-md mx-auto">
        
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-card p-8 sm:p-9 my-6 transition-all">
            
            <!-- Brand / Header -->
            <div class="text-center mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#123B70] text-white shadow-sm mb-3.5 hover:bg-[#2376D9] transition-colors">
                    <svg class="w-6 h-6 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </a>
                <h1 class="text-xl font-extrabold tracking-tight text-[#123B70]">
                    Daftar Akun <span class="text-[#2376D9]">Pelanggan</span>
                </h1>
                <p class="text-xs text-[#64748B] mt-1">
                    Bergabung bersama CargoTrace untuk visibilitas kargo real-time
                </p>
            </div>

            <!-- Validation Errors -->
            @if(isset($errors) && $errors->any())
                <div class="mb-5 flex items-start gap-2.5 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="leading-relaxed">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-[#172B4D] mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                           placeholder="Budi Santoso">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#172B4D] mb-1">Email Perusahaan / Korporat</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                           placeholder="budi@perusahaan.com">
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-semibold text-[#172B4D] mb-1">Nama Perusahaan / Shipper</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                           class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                           placeholder="PT Logistik Nusantara Prima">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#172B4D] mb-1">Nomor Telepon Kontak</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                           placeholder="+62 812 3456 7890">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#172B4D] mb-1">Kata Sandi</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#172B4D] mb-1">Konfirmasi Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3.5 py-2.5 bg-[#F4F7FB] hover:bg-white focus:bg-white text-[#172B4D] text-sm rounded-xl border border-[#E2E8F0] placeholder-[#64748B] focus:outline-none focus:border-[#2376D9] focus:ring-4 focus:ring-[#2376D9]/10 transition-all duration-150"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-3 bg-[#2376D9] hover:bg-[#1B5FBD] active:bg-[#164CA0] text-white rounded-xl font-bold text-sm shadow-sm transition-all duration-150 cursor-pointer focus:outline-none focus:ring-4 focus:ring-[#2376D9]/20">
                    Daftar Sekarang
                </button>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-[#E2E8F0] text-xs text-[#64748B]">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-[#2376D9] font-bold hover:text-[#123B70] hover:underline transition-colors ml-1">
                    Masuk di sini
                </a>
            </div>
        </div>

        <div class="mt-4 text-center text-[11px] text-[#64748B] flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-[#2376D9]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Pendaftaran Akun Terproteksi Enkripsi Standar Korporat</span>
        </div>
    </div>

</body>
</html>
