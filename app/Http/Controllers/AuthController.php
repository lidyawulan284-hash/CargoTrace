<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            AuditLogService::log(
                action: 'LOGIN',
                entityType: 'User',
                entityId: $user->id,
                oldValues: null,
                newValues: ['email' => $user->email, 'role' => $user->role]
            );

            $roleDashboardName = match ($user->role) {
                'admin' => 'Admin Dashboard',
                'operator' => 'Operator Dashboard',
                'customer' => 'Customer Dashboard',
                default => 'Dashboard',
            };

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang, {$user->name}! Anda berhasil masuk ke {$roleDashboardName}.");
        }

        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer', // Self-registered accounts are customers
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        AuditLogService::log(
            action: 'REGISTER',
            entityType: 'User',
            entityId: $user->id,
            oldValues: null,
            newValues: ['email' => $user->email, 'role' => $user->role]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Account registered successfully! Welcome to CargoTrace.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sesi telah diakhiri. Anda telah keluar dari sistem.');
    }

    /**
     * Demo one-click switch for evaluation/testing
     */
    public function demoLogin(string $role): RedirectResponse
    {
        $role = strtolower($role);
        $email = match ($role) {
            'admin' => 'admin@cargotrace.test',
            'operator' => 'operator@cargotrace.test',
            'customer' => 'customer@cargotrace.test',
            default => 'customer@cargotrace.test',
        };

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            $roleDashboardName = match ($user->role) {
                'admin' => 'Admin Dashboard (Akses Penuh)',
                'operator' => 'Operator Dashboard (Operasional & Telematika)',
                'customer' => 'Customer Dashboard (Kargo Pelanggan)',
                default => 'Dashboard',
            };

            return redirect()->route('dashboard')
                ->with('success', "Berhasil masuk ke {$roleDashboardName} sebagai {$user->name}.");
        }

        return redirect()->route('login')->with('error', 'Akun demo pengguna belum tersedia di database.');
    }
}
