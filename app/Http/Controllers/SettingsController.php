<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $activeTab = $request->query('tab', 'general');
        $allowedTabs = ['general', 'profile', 'account', 'notifications', 'language', 'security', 'preferences'];
        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'general';
        }

        $preferences = session('cargotrace_preferences', [
            'notify_status_updates' => true,
            'notify_exceptions' => true,
            'notify_delay_alerts' => true,
            'compact_tables' => false,
            'auto_follow_map' => true,
            'timezone' => config('app.timezone', 'UTC'),
        ]);

        return view('settings.index', compact('user', 'activeTab', 'preferences'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $old = $user->only(['name', 'company_name', 'phone']);
        $user->update($validated);

        AuditLogService::log(
            action: 'UPDATE_PROFILE',
            entityType: 'User',
            entityId: $user->id,
            oldValues: $old,
            newValues: $user->only(['name', 'company_name', 'phone']),
            actor: $user
        );

        return redirect()->route('settings.index', ['tab' => 'profile'])
            ->with('success', __('ui.settings.profile_saved'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLogService::log(
            action: 'CHANGE_PASSWORD',
            entityType: 'User',
            entityId: $user->id,
            oldValues: null,
            newValues: ['security_event' => 'Password updated by account owner'],
            actor: $user
        );

        return redirect()->route('settings.index', ['tab' => 'security'])
            ->with('success', __('ui.settings.password_saved'));
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $tab = $request->input('tab', 'preferences');
        $current = session('cargotrace_preferences', [
            'notify_status_updates' => true,
            'notify_exceptions' => true,
            'notify_delay_alerts' => true,
            'compact_tables' => false,
            'auto_follow_map' => true,
            'timezone' => config('app.timezone', 'UTC'),
        ]);

        if ($tab === 'notifications') {
            $current['notify_status_updates'] = $request->boolean('notify_status_updates');
            $current['notify_exceptions'] = $request->boolean('notify_exceptions');
            $current['notify_delay_alerts'] = $request->boolean('notify_delay_alerts');
        } else {
            $validated = $request->validate([
                'timezone' => ['nullable', 'string', 'max:60'],
            ]);
            $current['compact_tables'] = $request->boolean('compact_tables');
            $current['auto_follow_map'] = $request->boolean('auto_follow_map');
            if (! empty($validated['timezone'])) {
                $current['timezone'] = $validated['timezone'];
            }
        }

        session(['cargotrace_preferences' => $current]);

        return redirect()->route('settings.index', ['tab' => $tab])
            ->with('success', __('ui.settings.preferences_saved'));
    }
}
