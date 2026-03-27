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
            'bio'=>$bio->bio,
            'expert_area' => $expertise_area->expertise_area,
            'phone'=> $user->phone,
            'is_admin' => $user->role === 'admin',
            'is_mentor' => $user->role === 'mentor',
            
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