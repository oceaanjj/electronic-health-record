<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditLogController as AuditLogHelper;

class UserController extends Controller
{
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Doctor,Nurse,doctor,nurse',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role;
        $user->role = strtolower($request->role);
        $user->save();

        // ✅ Log into audit_logs
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => 'Changed role',
            'details'   => "User {$user->username}: {$oldRole} → {$user->role}",
            'created_at'=> now(),
        ]);

        return redirect()->route('users')
            ->with('success', "User {$user->username}'s role updated successfully from {$oldRole} to {$user->role}!");
    }

    public function index()
    {
        // Fetch all active users except admin
        $users = User::whereRaw('LOWER(role) != ?', ['admin'])->get();

        return view('admin.users', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = ['doctor', 'nurse']; // Consistent with RegisterController

        // Split full_name for the edit form
        $nameParts = explode(' ', $user->full_name ?? '');
        $firstName = $nameParts[0] ?? '';
        $lastName = (count($nameParts) > 1) ? implode(' ', array_slice($nameParts, 1)) : '';

        return view('admin.edit_user', compact('user', 'roles', 'firstName', 'lastName'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username'   => 'required|string|max:255|unique:users,username,' . $id,
            'email'      => 'required|email|max:255|unique:users,email,' . $id,
            'password'   => 'nullable|string|min:8|confirmed|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\x\W]).*$/',
            'role'       => 'required|in:doctor,nurse',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'birthdate'  => 'required|date',
            'sex'        => 'required|in:Male,Female,Other',
            'address'    => 'required|string|max:500',
            'birthplace' => 'required|string|max:255',
        ]);

        $data = [
            'username'   => $request->username,
            'email'      => $request->email,
            'role'       => strtolower($request->role),
            'full_name'  => trim($request->first_name . ' ' . $request->last_name),
            'birthdate'  => $request->birthdate,
            'sex'        => $request->sex,
            'address'    => $request->address,
            'birthplace' => $request->birthplace,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Recalculate Age
        $birthDate = new \DateTime($request->birthdate);
        $today = new \DateTime();
        $data['age'] = $today->diff($birthDate)->y;

        $user->update($data);

        // Audit Log
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => 'Updated user profile',
            'details'   => "Administrator updated profile for user: {$user->username}",
            'created_at'=> now(),
        ]);

        return redirect()->route('users')->with('success', "User {$user->username} updated successfully!");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $username = $user->username;
        $user->delete();

        // Log into audit_logs
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => 'Archived user',
            'details'   => "User {$username} has been archived.",
            'created_at'=> now(),
        ]);

        return redirect()->back()->with('success', "User {$username} has been archived successfully!");
    }

    public function archived()
    {
        $users = User::onlyTrashed()->whereRaw('LOWER(role) != ?', ['admin'])->get();
        return view('admin.archived_users', compact('users'));
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // Log into audit_logs
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => 'Restored user',
            'details'   => "User {$user->username} has been restored.",
            'created_at'=> now(),
        ]);

        return redirect()->back()->with('success', "User {$user->username} has been restored successfully!");
    }
}
