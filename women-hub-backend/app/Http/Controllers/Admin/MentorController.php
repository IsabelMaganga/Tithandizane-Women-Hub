<?php
// app/Http/Controllers/Admin/MentorController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MentorController extends Controller
{
    /**
     * Display a listing of mentors with search and filter capabilities
     */
    public function index(Request $request)
    {
        $query = Mentor::query();
        
        // Search by name, email, or expertise
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('expertise', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $perPage = $request->get('per_page', 10);
            $mentors = $query->latest()->paginate($perPage);
            
            // Get statistics
            $stats = [
                'total' => Mentor::count(),
                'active' => Mentor::where('status', 'active')->count(),
                'pending' => Mentor::where('status', 'pending')->count(),
                'inactive' => Mentor::where('status', 'inactive')->count(),
            ];
            
            // Format mentors for JSON response
            $mentors->getCollection()->transform(function ($mentor) {
                if (is_string($mentor->expertise)) {
                    $mentor->expertise = json_decode($mentor->expertise, true);
                }
                if (is_string($mentor->available_days)) {
                    $mentor->available_days = json_decode($mentor->available_days, true);
                }
                return $mentor;
            });
            
            return response()->json([
                'success' => true,
                'mentors' => $mentors->items(),
                'current_page' => $mentors->currentPage(),
                'last_page' => $mentors->lastPage(),
                'total' => $mentors->total(),
                'per_page' => $mentors->perPage(),
                'from' => $mentors->firstItem(),
                'to' => $mentors->lastItem(),
                'stats' => $stats,
            ]);
        }
        
        // For non-AJAX requests
        $mentors = $query->latest()->paginate(10);
        $stats = [
            'total' => Mentor::count(),
            'active' => Mentor::where('status', 'active')->count(),
            'pending' => Mentor::where('status', 'pending')->count(),
            'inactive' => Mentor::where('status', 'inactive')->count(),
        ];
        
        return view('admin.mentors.index', compact('mentors', 'stats'));
    }

    public function create()
    {
        $adminUser = Auth::guard('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Admin User';
        $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
        
        return view('admin.addmentors.addmentor', compact('adminName', 'adminEmail'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mentors,email',
                'password' => 'required|string|min:12|confirmed',
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:2048',
                'expertise' => 'required|array|min:1',
                'bio' => 'required|string|min:50|max:1000',
                'status' => 'required|in:active,pending,inactive',
                'availability' => 'nullable|string|max:500',
                'available_days' => 'nullable|array',
                'available_time_start' => 'nullable|date_format:H:i',
                'available_time_end' => 'nullable|date_format:H:i',
                'linkedin_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'website_url' => 'nullable|url',
                'notes' => 'nullable|string',
            ]);
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentor-photos', 'public');
                $validated['photo'] = $photoPath;
            }
            
            // Hash password
            $validated['password'] = Hash::make($validated['password']);
            
            // Convert expertise array to JSON
            $validated['expertise'] = json_encode($validated['expertise']);
            
            // Convert available_days array to JSON if present
            if (isset($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            }
            
            $mentor = Mentor::create($validated);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor created successfully',
                    'mentor' => $mentor
                ]);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('success', 'Mentor created successfully!');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Failed to create mentor: ' . $e->getMessage());
        }
    }

    public function show(Mentor $mentor)
    {
        return view('admin.mentors.show', compact('mentor'));
    }

    public function edit(Mentor $mentor)
    {
        $adminUser = Auth::guard('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Admin User';
        $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
        
        return view('admin.mentors.edit', compact('mentor', 'adminName', 'adminEmail'));
    }

    public function update(Request $request, Mentor $mentor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email,' . $mentor->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'expertise' => 'required|array|min:1',
            'bio' => 'required|string|min:50|max:1000',
            'status' => 'required|in:active,pending,inactive',
            'availability' => 'nullable|string|max:500',
            'available_days' => 'nullable|array',
            'available_time_start' => 'nullable|date_format:H:i',
            'available_time_end' => 'nullable|date_format:H:i',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($mentor->photo) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $photoPath = $request->file('photo')->store('mentor-photos', 'public');
            $validated['photo'] = $photoPath;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:12|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }
        
        // Convert expertise array to JSON
        $validated['expertise'] = json_encode($validated['expertise']);
        
        // Convert available_days array to JSON if present
        if (isset($validated['available_days'])) {
            $validated['available_days'] = json_encode($validated['available_days']);
        }
        
        $mentor->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mentor updated successfully',
                'mentor' => $mentor
            ]);
        }
        
        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor updated successfully!');
    }

    public function destroy(Mentor $mentor)
    {
        try {
            if ($mentor->photo) {
                Storage::disk('public')->delete($mentor->photo);
            }
            
            $mentor->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor deleted successfully'
                ]);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('success', 'Mentor deleted successfully!');
                
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('error', 'Failed to delete mentor!');
        }
    }

    public function toggleStatus(Mentor $mentor)
    {
        $mentor->status = $mentor->status === 'active' ? 'inactive' : 'active';
        $mentor->save();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mentor status updated successfully',
                'status' => $mentor->status
            ]);
        }
        
        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor status updated successfully!');
    }
}