<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - CargoTrace Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-slate-800">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 border border-slate-100 my-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Create Customer Account</h1>
            <p class="text-xs text-slate-500 mt-1">Join CargoTrace for real-time visibility into your cargo</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 text-rose-800 p-3 rounded-xl text-xs mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Corporate Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label for="company_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Company / Shipper Name</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                       placeholder="e.g. Global Tech Logistics PT">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Contact Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Confirm</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <button type="submit" class="w-full mt-2 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-semibold text-sm shadow-lg transition">
                Create Account
            </button>
        </form>

        <div class="text-center mt-6 text-xs text-slate-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-sky-600 font-semibold hover:underline">Sign In</a>
        </div>
    </div>

</body>
</html>
