<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegister()
    {
        return inertia('Auth/Register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users',
            'whatsapp_number'  => 'required|string|max:20',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            ...$data,
            'password' => bcrypt($data['password']),
            'role'     => 'user',
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
