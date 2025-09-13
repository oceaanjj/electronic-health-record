<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminModel;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        // Validate ng input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // check if yung name ba is exists sa database
        $admin = AdminModel::where('name', $request->name)->first();

        if (!$admin) {
            return back()->withErrors([
                'name' => 'No account exists with this name'
            ])->withInput();
        }

        // check if yung password is tama
        if (!Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'password' => 'Invalid password'
            ])->withInput();
        }

        // Save session
        session(['admin' => $admin]);

        // pag successful, redirect sa home
        return redirect()->route('home')->with('success', 'Login successful!');
    }
}
?>