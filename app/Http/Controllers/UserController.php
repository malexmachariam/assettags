<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:user,allocator,super_admin',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'active' => true,
        ]);
        return redirect()->route('users.index')->with('success', 'User added successfully.');
    }

    public function activate(User $user)
    {
        $user->active = true;
        $user->save();
        return redirect()->route('users.index')->with('success', 'User activated.');
    }

    public function deactivate(User $user)
    {
        $user->active = false;
        $user->save();
        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,allocator,super_admin',
        ]);
        $user->role = $validated['role'];
        $user->save();
        return redirect()->route('users.index')->with('success', 'User role updated.');
    }
}