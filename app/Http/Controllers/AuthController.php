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
                'admin' => __('ui.dashboard.role_admin_badge'),
                'operator' => __('ui.dashboard.role_operator_badge'),
                'customer' => __('ui.dashboard.role_customer_badge'),
                default => __('ui.dashboard.title'),
            };

            return redirect()->intended(route('dashboard'))
                ->with('success', __('ui.flash.login_success', ['name' => $user->name, 'dashboard' => $roleDashboardName]));
        }

        return back()->withErrors([
            'email' => __('ui.flash.login_failed'),
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
            ->with('success', __('ui.flash.register_success'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', __('ui.flash.logout_info'));
    }

    /**
     * Demo one-click switch for evaluation/testing
     */
    public function demoLogin(string $role): RedirectResponse
    {
        if (! config('cargotrace.demo_login_enabled', true)) {
            abort(403, 'Demo login is disabled in this environment.');
        }

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

            AuditLogService::log(
                action: 'DEMO_LOGIN',
                entityType: 'User',
                entityId: $user->id,
                oldValues: null,
                newValues: ['email' => $user->email, 'role' => $user->role],
                actor: $user
            );

            $roleDashboardName = match ($user->role) {
                'admin' => __('ui.dashboard.role_admin_badge'),
                'operator' => __('ui.dashboard.role_operator_badge'),
                'customer' => __('ui.dashboard.role_customer_badge'),
                default => __('ui.dashboard.title'),
            };

            return redirect()->route('dashboard')
                ->with('success', __('ui.flash.demo_login_success', ['dashboard' => $roleDashboardName, 'name' => $user->name]));
        }

        return redirect()->route('login')->with('error', __('ui.flash.demo_user_missing'));
    }
}
