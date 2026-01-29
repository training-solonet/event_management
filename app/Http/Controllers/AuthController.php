<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required']
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route("admin.admin.index");
        }

        if ($user->role === 'peserta') {
            return redirect()->route("home");
        }

        Auth::logout();
        abort(403, 'Role tidak dikenali');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah',
    ]);
}

}