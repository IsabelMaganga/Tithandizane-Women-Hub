<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Fetch a single user by ID
    public function getUser($id)
{
    $user = User::findOrFail($id);

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'photo_url' => $user->photo ? asset('storage/' . $user->photo) : asset('images/default-avatar.png'),
        'bio' => $user->bio,
        'expertise_area' => $user->expertise,
        'location' => $user->location,
        'role' => $user->role,
        'is_admin' => $user->isAdmin(),
        'is_mentor' => $user->isMentor(),
    ]);
}

    // Fetch all users (optionally excluding admins and mentors)
    public function getAllUsers()
{
    $users = User::whereNotIn('role', ['admin', 'mentor'])
        ->get(['id', 'name', 'email', 'role']);

    return response()->json($users);
}
}