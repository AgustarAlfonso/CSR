<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:1,2,3' // sesuaikan dengan role yang kamu pakai
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('auth.kelola')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
{
    return view('users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|confirmed|min:8',
        'role' => 'required|in:1,2,3'
    ]);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;

    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('auth.kelola')->with('success', 'Akun berhasil diperbarui!');
}

public function destroy($id)
{
    $user = User::findOrFail($id);

    // Optional: Tambahkan proteksi agar tidak menghapus diri sendiri atau superadmin
    if (auth()->user()->id == $user->id) {
        return redirect()->route('auth.kelola')->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
    }

    $user->delete();

    return redirect()->route('auth.kelola')->with('success', 'User berhasil dihapus.');
}
}
