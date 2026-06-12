<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Paginate results
        $users = $query->paginate(15);
        
        // Get statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $bannedUsers = User::where('status', 'banned')->count();
        
        // Get role distribution
        $roleStats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();
        
        return view('admin.users.index', compact('users', 'totalUsers', 'activeUsers', 'inactiveUsers', 'bannedUsers', 'roleStats'));
    }
    
    //delete user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent admin from deleting themselves
            if (auth()->guard('admin')->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own admin account.'
                ], 403);
            }
            
            
            $userName = $user->name;
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => "User '{$userName}' has been deleted successfully."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,banned'
        ]);
        
        try {
            $user = User::findOrFail($id);
            
            // Prevent admin from changing their own status
            if (auth()->guard('admin')->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own account status.'
                ], 403);
            }
            
            $user->status = $request->status;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => "User status updated to '{$request->status}' successfully."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status.'
            ], 500);
        }
    }
    
    /**
     * Get user details for AJAX request.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }
}