<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->withCount('shipments')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = trim((string) $request->input('search'));
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%"));
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $lastLogins = AuditLog::query()
            ->whereIn('action', ['LOGIN', 'DEMO_LOGIN'])
            ->whereIn('user_id', $users->pluck('id'))
            ->selectRaw('user_id, MAX(created_at) as last_login_at')
            ->groupBy('user_id')
            ->pluck('last_login_at', 'user_id');

        $stats = [
            'total' => User::count(),
            'active' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'others' => User::where('role', '!=', 'admin')->count(),
        ];

        return view('users.index', compact('users', 'lastLogins', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(['admin', 'operator', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        AuditLogService::log(
            action: 'CREATE',
            entityType: 'User',
            entityId: $user->id,
            oldValues: null,
            newValues: ['email' => $user->email, 'role' => $user->role, 'company_name' => $user->company_name]
        );

        return redirect()->route('users.index')->with('success', __('ui.flash.user_created', ['name' => $user->name, 'role' => __('ui.roles.'.$user->role)]));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in(['admin', 'operator', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
        ]);

        $old = $user->only(['name', 'role', 'phone', 'company_name']);
        $user->update($validated);

        AuditLogService::log(
            action: 'UPDATE',
            entityType: 'User',
            entityId: $user->id,
            oldValues: $old,
            newValues: $user->only(['name', 'role', 'phone', 'company_name'])
        );

        return redirect()->route('users.index')->with('success', __('ui.flash.user_updated', ['name' => $user->name]));
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $old = $user->only(['id', 'name', 'email', 'role']);
        $user->delete();

        AuditLogService::log(
            action: 'DELETE',
            entityType: 'User',
            entityId: $user->id,
            oldValues: $old,
            newValues: null
        );

        return redirect()->route('users.index')->with('success', __('ui.flash.user_deleted', ['email' => $user->email]));
    }
}
