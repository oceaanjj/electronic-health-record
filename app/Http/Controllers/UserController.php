<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Doctor,Nurse',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        // ✅ Log into audit_logs
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => 'Changed role',
            'details'   => "User {$user->username}: {$oldRole} → {$user->role}",
            'created_at'=> now(),
        ]);

        return redirect()->route('admin-home')
            ->with('success', "User {$user->username}'s role updated successfully from {$oldRole} to {$user->role}!");
    }

    public function index()
    {
        // Fetch all users except admin
        $users = User::where('role', '!=', 'Admin')->get();

        return view('admin.users', compact('users'));
    }
}
