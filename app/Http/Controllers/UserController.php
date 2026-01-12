<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        Gate::authorize('manage-users');
        return view('users.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-users');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:pemilik,karyawan',
        ]);

        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success','User berhasil dibuat');
    }

    public function edit($id)
    {
        Gate::authorize('manage-users');
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('manage-users');
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:pemilik,karyawan',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success','User diperbarui');
    }

    public function destroy($id)
    {
        Gate::authorize('manage-users');
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success','User dihapus');
    }
}
