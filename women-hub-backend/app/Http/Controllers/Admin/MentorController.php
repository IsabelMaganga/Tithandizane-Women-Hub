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
     * Display a listing of mentors for admin dashboard
     */
    public function index(Request $request)
    {
        // Handle AJAX requests for dashboard
        if ($request->expectsJson() || $request->ajax()) {
            $query = Mentor::query();
            
            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('bio', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $mentors = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Get stats for dashboard
            $stats = [
                'total' => Mentor::count(),
                'active' => Mentor::where('status', 'active')->count(),
                'pending' => Mentor::where('status', 'pending')->count(),
                'inactive' => Mentor::where('status', 'inactive')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'mentors' => $mentors->items(),
                'current_page' => $mentors->currentPage(),
                'last_page' => $mentors->lastPage(),
                'total' => $mentors->total(),
                'from' => $mentors->firstItem(),
                'to' => $mentors->lastItem(),
                'stats' => $stats
            ]);
        }
        
        // Handle normal web request
        $mentors = Mentor::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.mentors.index', compact('mentors'));
    }

    /**
     * Show the form for creating a new mentor
     */
    public function create()
    {
        // Get admin data for the view
        $adminName = Auth::guard('admin')->user()->name ?? 'Admin User';
        $adminEmail = Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org';
        
        return view('admin.mentors.create', compact('adminName', 'adminEmail'));
    }

    /**
     * Store a newly created mentor in storage
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mentors,email',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'location' => 'nullable|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'expertise' => 'nullable|array',
                'expertise.*' => 'string|max:100',
                'bio' => 'required|string',
                'availability' => 'nullable|string',
                'available_days' => 'nullable|array',
                'available_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'available_time_start' => 'nullable|date_format:H:i',
                'available_time_end' => 'nullable|date_format:H:i|after:available_time_start',
                'linkedin_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'website_url' => 'nullable|url',
                'notes' => 'nullable|string',
                'status' => 'sometimes|string|in:active,pending,inactive',
                'notify_welcome' => 'nullable|boolean',
                'notify_training' => 'nullable|boolean'
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentors', 'public');
                $validated['photo'] = $photoPath;
            }

            // Hash password
            $validated['password'] = Hash::make($validated['password']);
            
            // Convert arrays to JSON
            if (isset($validated['expertise']) && is_array($validated['expertise'])) {
                $validated['expertise'] = json_encode($validated['expertise']);
            } else {
                $validated['expertise'] = null;
            }
            
            if (isset($validated['available_days']) && is_array($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            } else {
                $validated['available_days'] = null;
            }
            
            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = 'pending';
            }
            
            // Remove password_confirmation if exists
            unset($validated['password_confirmation']);
            
            // Remove notification fields (not in database)
            unset($validated['notify_welcome']);
            unset($validated['notify_training']);
            
            $mentor = Mentor::create($validated);
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor created successfully',
                    'mentor' => $mentor
                ]);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('success', 'Mentor created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to create mentor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified mentor (admin view)
     */
    public function show($id)
    {
        $mentor = Mentor::findOrFail($id);
        
        // Decode JSON fields for display
        if (is_string($mentor->expertise)) {
            $mentor->expertise = json_decode($mentor->expertise, true);
        }
        
        if (is_string($mentor->available_days)) {
            $mentor->available_days = json_decode($mentor->available_days, true);
        }
        
        // Check if request expects JSON (AJAX)
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'mentor' => $mentor
            ]);
        }
        
        // For web view
        return view('admin.mentors.show', compact('mentor'));
    }

    /**
     * Show the form for editing the specified mentor
     */
    public function edit($id)
    {
        $mentor = Mentor::findOrFail($id);
        
        // Decode JSON fields for form
        if (is_string($mentor->expertise)) {
            $mentor->expertise = json_decode($mentor->expertise, true);
        } else if (!is_array($mentor->expertise)) {
            $mentor->expertise = [];
        }
        
        if (is_string($mentor->available_days)) {
            $mentor->available_days = json_decode($mentor->available_days, true);
        } else if (!is_array($mentor->available_days)) {
            $mentor->available_days = [];
        }
        
        $adminName = Auth::guard('admin')->user()->name ?? 'Admin User';
        $adminEmail = Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org';
        
        // Changed to use admin.mentors.edit instead of admin.addmentors.editmentor
        return view('admin.mentors.edit', compact('mentor', 'adminName', 'adminEmail'));
    }

    /**
     * Update the specified mentor in storage
     */
    public function update(Request $request, $id)
    {
        try {
            $mentor = Mentor::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mentors,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'expertise' => 'nullable|array',
                'expertise.*' => 'string|max:100',
                'bio' => 'required|string',
                'availability' => 'nullable|string',
                'available_days' => 'nullable|array',
                'available_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'available_time_start' => 'nullable|date_format:H:i',
                'available_time_end' => 'nullable|date_format:H:i|after:available_time_start',
                'linkedin_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'website_url' => 'nullable|url',
                'notes' => 'nullable|string',
                'status' => 'sometimes|string|in:active,pending,inactive'
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                    Storage::disk('public')->delete($mentor->photo);
                }
                
                $photoPath = $request->file('photo')->store('mentors', 'public');
                $validated['photo'] = $photoPath;
            }
            
            // Convert arrays to JSON
            if (isset($validated['expertise']) && is_array($validated['expertise'])) {
                $validated['expertise'] = json_encode($validated['expertise']);
            } else {
                $validated['expertise'] = $mentor->expertise;
            }
            
            if (isset($validated['available_days']) && is_array($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            } else {
                $validated['available_days'] = $mentor->available_days;
            }
            
            $mentor->update($validated);
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor updated successfully',
                    'mentor' => $mentor
                ]);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('success', 'Mentor updated successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update mentor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified mentor from storage
     */
    public function destroy($id)
    {
        try {
            $mentor = Mentor::findOrFail($id);
            
            // Delete photo if exists
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }
            
            $mentor->delete();
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor deleted successfully'
                ]);
            }
            
            return redirect()->route('admin.mentors.index')
                ->with('success', 'Mentor deleted successfully.');
                
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to delete mentor: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle mentor status (active/inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $mentor = Mentor::findOrFail($id);
            
            $newStatus = $mentor->status === 'active' ? 'inactive' : 'active';
            $mentor->status = $newStatus;
            $mentor->save();
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor status updated successfully',
                    'status' => $newStatus
                ]);
            }
            
            return back()->with('success', 'Mentor status updated successfully.');
            
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get mentor statistics for dashboard
     */
    public function getMentorStats(Request $request)
    {
        try {
            $stats = [
                'total' => Mentor::count(),
                'active' => Mentor::where('status', 'active')->count(),
                'pending' => Mentor::where('status', 'pending')->count(),
                'inactive' => Mentor::where('status', 'inactive')->count(),
                'new_this_month' => Mentor::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'total_sessions' => Mentor::sum('total_sessions') ?? 0,
                'avg_rating' => Mentor::whereNotNull('rating')->avg('rating') ?? 0,
            ];
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'stats' => $stats
                ]);
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            \Log::error('Error in getMentorStats: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch stats'
                ], 500);
            }
            
            return [];
        }
    }

    /**
     * Get active mentors for frontend (React Native) - API endpoint
     */
    public function getActiveMentors(Request $request)
    {
        try {
            \Log::info('API getActiveMentors called', [
                'search' => $request->search,
                'expertise' => $request->expertise
            ]);
            
            $query = Mentor::where('status', 'active')
                ->orderBy('name', 'asc');
            
            // Optional: Search by name or expertise
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('expertise', 'like', "%{$search}%")
                      ->orWhere('bio', 'like', "%{$search}%");
                });
            }
            
            // Optional: Filter by expertise
            if ($request->has('expertise') && !empty($request->expertise)) {
                $expertise = $request->expertise;
                $query->where('expertise', 'like', "%{$expertise}%");
            }
            
            $mentors = $query->get();
            
            \Log::info('Found mentors count: ' . $mentors->count());
            
            // Format mentors for frontend
            $formattedMentors = $mentors->map(function($mentor) {
                // Decode expertise if it's JSON
                $expertise = $mentor->expertise;
                if (is_string($expertise)) {
                    $expertise = json_decode($expertise, true);
                }
                if (!is_array($expertise)) {
                    $expertise = [];
                }
                
                // Decode available days if it's JSON
                $availableDays = $mentor->available_days;
                if (is_string($availableDays)) {
                    $availableDays = json_decode($availableDays, true);
                }
                if (!is_array($availableDays)) {
                    $availableDays = [];
                }
                
                return [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'email' => $mentor->email,
                    'phone' => $mentor->phone,
                    'location' => $mentor->location,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'avatar' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'expertise' => $expertise,
                    'bio' => $mentor->bio ?? '',
                    'availability' => $mentor->availability,
                    'available_days' => $availableDays,
                    'available_time_start' => $mentor->available_time_start,
                    'available_time_end' => $mentor->available_time_end,
                    'linkedin_url' => $mentor->linkedin_url,
                    'twitter_url' => $mentor->twitter_url,
                    'website_url' => $mentor->website_url,
                    'rating' => $mentor->rating ?? null,
                    'total_sessions' => $mentor->total_sessions ?? 0,
                    'status' => $mentor->status,
                    'created_at' => $mentor->created_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Mentors retrieved successfully',
                'mentors' => $formattedMentors,
                'total' => $formattedMentors->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getActiveMentors: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch mentors: ' . $e->getMessage(),
                'mentors' => []
            ], 500);
        }
    }
    
    /**
     * Get a single mentor details for frontend
     */
    public function getMentorDetails($id)
    {
        try {
            $mentor = Mentor::where('id', $id)
                ->where('status', 'active')
                ->first();
            
            if (!$mentor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mentor not found'
                ], 404);
            }
            
            // Decode expertise if it's JSON
            $expertise = $mentor->expertise;
            if (is_string($expertise)) {
                $expertise = json_decode($expertise, true);
            }
            if (!is_array($expertise)) {
                $expertise = [];
            }
            
            // Decode available days if it's JSON
            $availableDays = $mentor->available_days;
            if (is_string($availableDays)) {
                $availableDays = json_decode($availableDays, true);
            }
            if (!is_array($availableDays)) {
                $availableDays = [];
            }
            
            $formattedMentor = [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'email' => $mentor->email,
                'phone' => $mentor->phone,
                'location' => $mentor->location,
                'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                'avatar' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                'expertise' => $expertise,
                'bio' => $mentor->bio ?? '',
                'availability' => $mentor->availability,
                'available_days' => $availableDays,
                'available_time_start' => $mentor->available_time_start,
                'available_time_end' => $mentor->available_time_end,
                'linkedin_url' => $mentor->linkedin_url,
                'twitter_url' => $mentor->twitter_url,
                'website_url' => $mentor->website_url,
                'notes' => $mentor->notes,
                'rating' => $mentor->rating ?? null,
                'total_sessions' => $mentor->total_sessions ?? 0,
                'created_at' => $mentor->created_at,
            ];
            
            return response()->json([
                'success' => true,
                'mentor' => $formattedMentor
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getMentorDetails: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch mentor details'
            ], 500);
        }
    }
}