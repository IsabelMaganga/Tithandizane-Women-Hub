<?php
// app/Http/Controllers/Admin/MentorController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MentorStoreRequest;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        if ($request->has('status') && !empty($request->status) && in_array($request->status, ['active', 'pending', 'inactive'])) {
            $query->where('status', $request->status);
        }
        
        // Check if it's an AJAX request (for the dashboard and index page live updates)
        if ($request->ajax() || $request->wantsJson()) {
            $perPage = $request->get('per_page', 10);
            $mentors = $query->latest()->paginate($perPage);
            
            // Get statistics for dashboard
            $stats = [
                'total' => Mentor::count(),
                'active' => Mentor::where('status', 'active')->count(),
                'pending' => Mentor::where('status', 'pending')->count(),
                'inactive' => Mentor::where('status', 'inactive')->count(),
            ];
            
            // Format mentors for JSON response
            $mentors->getCollection()->transform(function ($mentor) {
                // Decode expertise if it's a JSON string
                if (is_string($mentor->expertise)) {
                    $mentor->expertise = json_decode($mentor->expertise, true);
                }
                // Decode available_days if it's a JSON string
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
        
        // For non-AJAX requests, return the view
        $mentors = $query->latest()->paginate(10);
        return view('admin.mentors.index', compact('mentors'));
    }

    public function create()
    {
        // Get the currently authenticated admin user
        $adminUser = Auth::guard('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Admin User';
        $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
        
        // Pass admin data to the view
        return view('admin.addmentors.addmentor', compact('adminName', 'adminEmail'));
    }

    public function store(MentorStoreRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Hash the password
            $data['password'] = Hash::make($data['password']);
            
            // Handle expertise as JSON (it comes as an array from the form)
            if (isset($data['expertise']) && is_array($data['expertise'])) {
                $data['expertise'] = json_encode($data['expertise']);
            }
            
            // Handle available_days as JSON (if provided)
            if (isset($data['available_days']) && is_array($data['available_days'])) {
                $data['available_days'] = json_encode($data['available_days']);
            }
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentor-photos', 'public');
                $data['photo'] = $photoPath;
            }
            
            // Handle boolean checkboxes (they don't send value when unchecked)
            $data['notify_welcome'] = $request->has('notify_welcome');
            $data['notify_training'] = $request->has('notify_training');
            
            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }
            
            // Create the mentor
            $mentor = Mentor::create($data);
            
            // Send welcome email if requested
            if ($data['notify_welcome']) {
                // You can implement email sending here
                // Mail::to($mentor->email)->send(new MentorWelcomeMail($mentor));
            }
            
            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor created successfully!',
                    'mentor' => $mentor
                ], 201);
            }
            
            return redirect()
                ->route('admin.mentors.index')
                ->with('success', 'Mentor created successfully!');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create mentor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified mentor (web view)
     */
    public function show(Mentor $mentor)
    {
        // Decode expertise for display
        if ($mentor->expertise) {
            $expertiseArray = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
            $mentor->expertise_list = is_array($expertiseArray) ? $expertiseArray : [];
        }
        
        // Decode available_days for display
        if ($mentor->available_days) {
            $daysArray = is_string($mentor->available_days) ? json_decode($mentor->available_days, true) : $mentor->available_days;
            $mentor->available_days_list = is_array($daysArray) ? $daysArray : [];
        }
        
        return view('admin.mentors.show', compact('mentor'));
    }

    /**
     * Show the form for editing the specified mentor
     */
    public function edit(Mentor $mentor)
    {
        // Get admin user data
        $adminUser = Auth::guard('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Admin User';
        $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
        
        // Decode expertise for form display
        if ($mentor->expertise) {
            $expertiseArray = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
            $mentor->expertise_array = is_array($expertiseArray) ? $expertiseArray : [];
        }
        
        // Decode available_days for form display
        if ($mentor->available_days) {
            $daysArray = is_string($mentor->available_days) ? json_decode($mentor->available_days, true) : $mentor->available_days;
            $mentor->available_days_array = is_array($daysArray) ? $daysArray : [];
        }
        
        return view('admin.mentors.edit', compact('mentor', 'adminName', 'adminEmail'));
    }

    /**
     * Update the specified mentor in storage (web)
     */
    public function update(Request $request, Mentor $mentor)
    {
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email,' . $mentor->id,
            'expertise' => 'required|array|min:1',
            'bio' => 'required|string|max:500',
            'status' => 'required|in:pending,active,inactive',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'available_days' => 'nullable|array',
            'available_time_start' => 'nullable|string|max:5',
            'available_time_end' => 'nullable|string|max:5',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        // Add password validation only if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:12|confirmed';
        }
        
        $request->validate($rules);
        
        $data = $request->except(['password', 'password_confirmation', '_token', '_method']);
        
        // Handle password update if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $data['photo'] = $request->file('photo')->store('mentor-photos', 'public');
        }
        
        // Handle expertise as JSON
        if (isset($data['expertise']) && is_array($data['expertise'])) {
            $data['expertise'] = json_encode($data['expertise']);
        }
        
        // Handle available_days as JSON
        if (isset($data['available_days']) && is_array($data['available_days'])) {
            $data['available_days'] = json_encode($data['available_days']);
        }
        
        // Handle boolean checkboxes
        $data['notify_welcome'] = $request->has('notify_welcome');
        $data['notify_training'] = $request->has('notify_training');
        
        $mentor->update($data);
        
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mentor updated successfully!',
                'mentor' => $mentor
            ]);
        }
        
        return redirect()
            ->route('admin.mentors.index')
            ->with('success', 'Mentor updated successfully!');
    }

    /**
     * Remove the specified mentor from storage (web)
     */
    public function destroy(Mentor $mentor)
    {
        try {
            // Delete photo if exists
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }
            
            $mentor->delete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor deleted successfully!'
                ]);
            }
            
            return redirect()
                ->route('admin.mentors.index')
                ->with('success', 'Mentor deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->route('admin.mentors.index')
                ->with('error', 'Failed to delete mentor!');
        }
    }

    /**
     * Get all mentors for API (frontend consumption)
     */
    public function getActiveMentors()
    {
        $mentors = Mentor::where('status', 'active')
            ->select(
                'id',
                'name',
                'bio',
                'expertise',
                'availability',
                'available_days',
                'available_time_start',
                'available_time_end',
                'photo as avatar',
                'email',
                'phone',
                'location'
            )
            ->latest()
            ->get()
            ->map(function ($mentor) {
                // Decode expertise JSON to string for frontend
                if ($mentor->expertise) {
                    $expertiseArray = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
                    $mentor->expertise_area = is_array($expertiseArray) ? implode(', ', $expertiseArray) : $mentor->expertise;
                }
                
                // Decode available_days
                if ($mentor->available_days) {
                    $mentor->available_days = is_string($mentor->available_days) ? json_decode($mentor->available_days, true) : $mentor->available_days;
                }
                
                // Add full URL for avatar
                if ($mentor->avatar && !str_starts_with($mentor->avatar, 'http')) {
                    $mentor->avatar = asset('storage/' . $mentor->avatar);
                }
                
                return $mentor;
            });

        return response()->json($mentors);
    }
}