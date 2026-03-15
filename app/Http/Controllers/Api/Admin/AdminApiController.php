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
            'total_users'      => User::count(),
            'total_nurses'     => User::whereRaw('LOWER(role) = ?', ['nurse'])->count(),
            'total_doctors'    => User::whereRaw('LOWER(role) = ?', ['doctor'])->count(),
            'total_admins'     => User::whereRaw('LOWER(role) = ?', ['admin'])->count(),
            'total_patients'   => Patient::count(),
            'active_patients'  => Patient::where('is_active', true)->count(),
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
                ->orWhere('full_name', 'like', "%$s%")
            );
        }

        $users = $query->orderBy('username')->get()->map(fn($u) => [
            'id'         => $u->id,
            'username'   => $u->username,
            'email'      => $u->email,
            'role'       => strtolower((string) $u->role),
            'full_name'  => $u->full_name,
            'created_at' => $u->created_at->format('Y-m-d H:i:s'),
        ]);

        return response()->json($users);
    }

    // ─────────────────────────────────────────────
    // Get Single User
    // GET /api/admin/users/{id}
    // ─────────────────────────────────────────────
    public function showUser($id)
    {
        $u = User::findOrFail($id);
        return response()->json([
            'id'          => $u->id,
            'username'    => $u->username,
            'email'       => $u->email,
            'role'        => strtolower((string) $u->role),
            'full_name'   => $u->full_name,
            'birthdate'   => $u->birthdate,
            'age'         => $u->age,
            'sex'         => $u->sex,
            'address'     => $u->address,
            'birthplace'  => $u->birthplace,
            'created_at'  => $u->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    // ─────────────────────────────────────────────
    // Register New User
    // POST /api/admin/users
    // ─────────────────────────────────────────────
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'username'   => 'required|string|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|in:nurse,doctor,admin',
            'full_name'  => 'nullable|string',
            'birthdate'  => 'nullable|date',
            'age'        => 'nullable|integer',
            'sex'        => 'nullable|string',
            'address'    => 'nullable|string',
            'birthplace' => 'nullable|string',
        ]);

        $user = User::create([
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => strtolower($validated['role']),
            'full_name'  => $validated['full_name'] ?? null,
            'birthdate'  => $validated['birthdate'] ?? null,
            'age'        => $validated['age'] ?? null,
            'sex'        => $validated['sex'] ?? null,
            'address'    => $validated['address'] ?? null,
            'birthplace' => $validated['birthplace'] ?? null,
        ]);

        AuditLogController::log(
            'USER REGISTRATION',
            "Administrator " . Auth::user()->username . " successfully registered a new user: {$user->username} with role " . strtoupper($user->role) . ".",
            ['new_user_id' => $user->id]
        );

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // Update User Details
    // PUT /api/admin/users/{id}
    // ─────────────────────────────────────────────
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username'   => 'sometimes|string|unique:users,username,' . $id,
            'email'      => 'sometimes|email|unique:users,email,' . $id,
            'password'   => 'sometimes|string|min:8|nullable',
            'role'       => 'sometimes|in:nurse,doctor,admin',
            'full_name'  => 'nullable|string',
            'birthdate'  => 'nullable|date',
            'age'        => 'nullable|integer',
            'sex'        => 'nullable|string',
            'address'    => 'nullable|string',
            'birthplace' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        AuditLogController::log(
            'USER UPDATED',
            "Administrator " . Auth::user()->username . " updated the profile of user: {$user->username}.",
            ['updated_user_id' => $user->id]
        );

        return response()->json([
            'message' => 'User updated successfully.',
            'user'    => $user,
        ]);
    }

    // ─────────────────────────────────────────────
    // Update User Role Only
    // PATCH /api/admin/users/{id}/role
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
            'ROLE CHANGED',
            "Administrator " . Auth::user()->username . " changed role of {$user->username} from " . strtoupper($oldRole) . " to " . strtoupper($validated['role']) . ".",
            ['user_id' => $user->id]
        );

        return response()->json([
            'message' => 'Role updated successfully.',
            'user'    => ['id' => $user->id, 'username' => $user->username, 'role' => strtolower((string) $user->role)],
        ]);
    }

    // ─────────────────────────────────────────────
    // Audit Logs
    // GET /api/admin/audit-logs
    // ─────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $query = AuditLog::query();

        if ($request->query('search')) {
            $s = $request->query('search');
            $query->where(fn($q) => $q
                ->where('user_name', 'like', "%$s%")
                ->orWhere('action', 'like', "%$s%")
                ->orWhere('details', 'like', "%$s%")
            );
        }

        $sort = $request->query('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sort);

        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())->map(function($log) {
            $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
            $sentence = $details['details'] ?? 'No details provided.';

            return [
                'id'           => $log->id,
                'user_name'    => $log->user_name,
                'user_role'    => strtoupper((string) $log->user_role),
                'action'       => $log->action,
                'sentence'     => $sentence,
                'date'         => $log->created_at->format('Y-m-d'),
                'time'         => $log->created_at->format('H:i:s'),
                'created_at'   => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data'      => $items,
            'total'     => $paginated->total(),
            'page'      => $paginated->currentPage(),
            'per_page'  => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
        ]);
    }
}
