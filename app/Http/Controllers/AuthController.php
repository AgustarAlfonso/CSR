<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai role
            $user = Auth::user();
            if ($user->role == 1 || $user->role == 2 || $user->role == 3) {
                return redirect()->route('dashboard');
            }

            // Default fallback
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function kelolaUser()
{
    // Ambil semua user dari database
    $users = \App\Models\User::all();

    // Kirim ke view
    return view('auth.kelola', compact('users'));
}

}
