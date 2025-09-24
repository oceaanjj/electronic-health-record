<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs with search and sorting capabilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AuditLog::query();

        // Handle username search filter
        if ($request->has('username_search') && $request->input('username_search') != '') {
            $username = $request->input('username_search');
            $query->where('user_name', 'like', '%' . $username . '%');
        }

        // Handle sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }

    public static function log(string $action, string $details, array $extra_data = []): void
    {
        $user = Auth::user();

        AuditLog::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->username ?? 'System',
            'user_role' => $user->role ?? 'Guest',
            'action' => $action,
            'details' => json_encode(array_merge(['details' => $details], $extra_data)),
            'ip_address' => request()->ip(),
        ]);
    }
}
