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

            AuditLogService::log(
                action: 'LOGIN',
                entityType: 'User',
                entityId: Auth::id(),
                oldValues: null,
                newValues: ['email' => Auth::user()->email]
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
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

        return redirect()->route('login')->with('info', 'You have been logged out.');
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
            return redirect()->route('dashboard')
                ->with('success', "Logged in as demo {$user->role} ({$user->name}).");
        }

        return redirect()->route('login')->with('error', 'Demo user not seeded yet.');
    }
}
