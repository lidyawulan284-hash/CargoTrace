<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = AuditLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->paginate(25);

        return view('audit_logs.index', compact('logs'));
    }
}
