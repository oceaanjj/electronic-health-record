<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NurseModel;
use Illuminate\Support\Facades\Hash;

class NurseController extends Controller
{
    public function login(Request $request)
    {
        // Validate ng input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // check if yung name ba is exists sa database
        $nurse = NurseModel::where('name', $request->name)->first();

        if (!$nurse) {
            return back()->withErrors([
                'name' => 'No account exists with this name'
            ])->withInput();
        }

        // check if yung password is tama
        if (!Hash::check($request->password, $nurse->password)) {
            return back()->withErrors([
                'password' => 'Invalid password'
            ])->withInput();
        }

        // Save session
        session(['nurse' => $nurse]);

        // pag successful, redirect sa home
        return redirect()->route('home')->with('success', 'Login successful!');
    }
}
?>