<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuditLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Patient;

class AdminApiController extends Controller
{
    // ─────────────────────────────────────────────
    // Admin Dashboard Stats
    // GET /api/admin/stats
    // ─────────────────────────────────────────────
    public function stats()
    {
        return response()->json([
            'total_users'    => User::count(),
            'total_nurses'   => User::whereRaw('LOWER(role) = ?', ['nurse'])->count(),
            'total_doctors'  => User::whereRaw('LOWER(role) = ?', ['doctor'])->count(),
            'total_admins'   => User::whereRaw('LOWER(role) = ?', ['admin'])->count(),
            'total_patients' => Patient::count(),
            'active_patients'=> Patient::where('is_active', true)->count(),
            'audit_logs_today' => AuditLog::whereDate('created_at', today())->count(),
        ]);
    }

    // ─────────────────────────────────────────────
    // List All Users
    // GET /api/admin/users?role=nurse&search=
    // ─────────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->query('role')) {
            $query->whereRaw('LOWER(role) = ?', [strtolower((string) $request->query('role'))]);
        }

        if ($request->query('search')) {
            $s = $request->query('search');
            $query->where(fn($q) => $q
                ->where('username', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
            );
        }

        $users = $query->orderBy('username')->get()->map(fn($u) => [
            'id'         => $u->id,
            'username'   => $u->username,
            'email'      => $u->email,
            'role'       => strtolower((string) $u->role),
            'created_at' => (string) $u->created_at,
        ]);

        return response()->json($users);
    }

    // ─────────────────────────────────────────────
    // Get Single User
    // GET /api/admin/users/{id}
    // ─────────────────────────────────────────────
    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'id'         => $user->id,
            'username'   => $user->username,
            'email'      => $user->email,
            'role'       => strtolower((string) $user->role),
            'created_at' => (string) $user->created_at,
        ]);
    }

    // ─────────────────────────────────────────────
    // Register New User
    // POST /api/admin/users
    // Body: username, email, password, role (nurse|doctor|admin)
    // ─────────────────────────────────────────────
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:nurse,doctor,admin',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => strtolower($validated['role']),
        ]);

        AuditLogController::log(
            'User Registered (API)',
            'Admin ' . Auth::user()->username . ' registered new user via API.',
            ['new_user_id' => $user->id, 'role' => $user->role]
        );

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => ['id' => $user->id, 'username' => $user->username, 'email' => $user->email, 'role' => strtolower((string) $user->role)],
        ], 201);
    }

    // ─────────────────────────────────────────────
    // Update User Role
    // PATCH /api/admin/users/{id}/role
    // Body: role (nurse|doctor|admin)
    // ─────────────────────────────────────────────
    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:nurse,doctor,admin',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role;
        $user->update(['role' => strtolower($validated['role'])]);

        AuditLogController::log(
            'User Role Updated (API)',
            'Admin ' . Auth::user()->username . " changed user {$user->username} role from {$oldRole} to {$validated['role']} via API.",
            ['user_id' => $user->id]
        );

        return response()->json([
            'message' => 'Role updated successfully.',
            'user'    => ['id' => $user->id, 'username' => $user->username, 'role' => strtolower((string) $user->role)],
        ]);
    }

    // ─────────────────────────────────────────────
    // Audit Logs
    // GET /api/admin/audit-logs?search=&sort=desc&page=1&per_page=20
    // ─────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $query = AuditLog::query();

        if ($request->query('search')) {
            $s = $request->query('search');
            $query->where(fn($q) => $q
                ->where('user_name', 'like', "%$s%")
                ->orWhere('action', 'like', "%$s%")
            );
        }

        $sort = $request->query('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sort);

        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data'      => $paginated->items(),
            'total'     => $paginated->total(),
            'page'      => $paginated->currentPage(),
            'per_page'  => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
        ]);
    }
}
