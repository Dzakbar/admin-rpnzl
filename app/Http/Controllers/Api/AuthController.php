<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'whatsapp_number' => 'required|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp_number' => $data['whatsapp_number'],
            'password' => $data['password'],
            'role' => 'user',
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->role !== 'user') {
            return response()->json([
                'message' => 'Akun admin tidak dapat digunakan di halaman pelanggan.',
            ], 403);
        }

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $this->serializeUser($user),
        ]);
    }

    public function googleLogin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'picture' => 'nullable|url|max:1000',
        ]);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Str::password(32),
                'role' => 'user',
            ],
        );

        if ($user->role !== 'user') {
            return response()->json([
                'message' => 'Akun admin tidak dapat digunakan di halaman pelanggan.',
            ], 403);
        }

        if ($user->name !== $data['name']) {
            $user->forceFill(['name' => $data['name']])->save();
        }

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => [
                ...$this->serializeUser($user),
                'picture' => $data['picture'] ?? null,
                'provider' => 'google',
            ],
        ]);
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'whatsapp_number' => $user->whatsapp_number,
            'phone' => $user->whatsapp_number,
            'provider' => 'email',
        ];
    }
}
