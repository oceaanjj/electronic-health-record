<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorModel;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function login(Request $request)
    {
        // Validate ng input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // check if yung name ba is exists sa database
        $doctor = DoctorModel::where('name', $request->name)->first();

        if (!$doctor) {
            return back()->withErrors([
                'name' => 'No account exists with this name'
            ])->withInput();
        }

        // check if yung password is tama
        if (!Hash::check($request->password, $doctor->password)) {
            return back()->withErrors([
                'password' => 'Invalid password'
            ])->withInput();
        }

        // Save session
        session(['doctor' => $doctor]);

        // pag successful, redirect sa home
        return redirect()->route('home')->with('success', 'Login successful!');
    }
}
?>