<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderby('id_user')->get();
        return view('superadmin.user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'no_hp'   => 'required|string|max:15',
            'role'     => 'required|in:super admin,kadiv,kadept,staf',
        ]);

        User::create([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'no_hp' => $validated['no_hp'],
            'role' => $validated['role'],
        ]);

        return redirect()->back()->with('success', 'User created successfully!');
    }
    
    public function update(Request $request, $id_user)
    {

        $user = User::findOrFail($id_user);

        if ($id_user == Auth::id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id_user . ',id_user',
            'no_hp'   => 'required|string|max:15',
            'role'  => 'required|in:super admin,kadiv,kadept,staf',
        ]);

        // Update data dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'];
        $user->role = $validated['role'];

        // Jika password diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroy($id_user)
    {
        if ($id_user == Auth::id()) 
        {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        $user = User::findOrFail($id_user);
        $user->delete();

        

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
