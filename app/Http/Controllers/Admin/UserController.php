<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount('bookings')
            ->latest()
            ->paginate(20)
            ->through(fn($u) => [
                'id'              => $u->id,
                'name'            => $u->name,
                'email'           => $u->email,
                'whatsapp_number' => $u->whatsapp_number,
                'bookings_count'  => $u->bookings_count,
                'created_at'      => $u->created_at->format('d M Y'),
            ]);

        return inertia('Admin/Users/Index', compact('users'));
    }
}
