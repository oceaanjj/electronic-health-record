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
            'role' => 'required|in:Admin,Doctor,Nurse',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        // // log into audit_logs
        // AuditLog::create([
        //     'id' => Auth::id(),
        //     'action'    => 'Changed role',
        //     'details'   => "User {$user->name}: {$oldRole} → {$user->role}",
        //     'created_at'=> now(),
        // ]);


        AuditLogController::log('Patient Deleted', 'User ' . Auth::user()->username . ' deleted patient record.', ['patient_id' => $id]);
        return redirect()->route('admin-home')->with('success', 'User role updated successfully!');
    }

}
