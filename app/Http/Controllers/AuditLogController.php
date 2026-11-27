<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = $this->buildFilteredQuery($request);

        $logs = $query->paginate(25)->withQueryString();
        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::query()->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        $stats = [
            'total' => AuditLog::count(),
            'logins' => AuditLog::whereIn('action', ['LOGIN', 'DEMO_LOGIN', 'REGISTER'])->count(),
            'changes' => AuditLog::whereIn('action', [
                'CREATE', 'UPDATE', 'DELETE', 'STATUS_TRANSITION',
                'CREATE_TRACKING_EVENT', 'CREATE_EXCEPTION', 'INVESTIGATE_EXCEPTION', 'RESOLVE_EXCEPTION',
            ])->count(),
            'security' => AuditLog::whereIn('action', ['DELETE', 'CHANGE_PASSWORD', 'DEMO_LOGIN', 'SYSTEM_INIT'])->count(),
        ];

        return view('audit_logs.index', compact('logs', 'actions', 'entityTypes', 'users', 'stats'));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $logs = $this->buildFilteredQuery($request)->limit(1000)->get();

        return response()->streamDownload(function () use ($logs): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Timestamp', 'Actor', 'Action', 'Module', 'Entity ID', 'IP Address', 'Old Values', 'New Values']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->created_at?->toDateTimeString(),
                    $log->user_name,
                    $log->action,
                    $log->entity_type,
                    $log->entity_id,
                    $log->ip_address,
                    $log->old_values ? json_encode($log->old_values) : '',
                    $log->new_values ? json_encode($log->new_values) : '',
                ]);
            }
            fclose($out);
        }, 'cargotrace-audit-logs-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildFilteredQuery(Request $request)
    {
        $query = AuditLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('user_name', 'like', "%{$term}%")
                    ->orWhere('action', 'like', "%{$term}%")
                    ->orWhere('entity_type', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', (string) $request->input('action'));
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', (string) $request->input('entity_type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', (string) $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', (string) $request->input('date_to'));
        }

        return $query;
    }
}
