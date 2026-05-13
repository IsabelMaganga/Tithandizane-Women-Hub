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
            $mentors = Mentor::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'mentors' => $mentors
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
        
        return view('admin.addmentors.addmentor', compact('adminName', 'adminEmail'));
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

    // ... rest of your existing methods (show, edit, update, destroy, toggleStatus, getMentorStats, etc.)
}