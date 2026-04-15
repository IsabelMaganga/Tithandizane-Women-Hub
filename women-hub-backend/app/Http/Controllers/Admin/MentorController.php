<?php

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
        
        // Search by name or email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status) && in_array($request->status, ['active', 'pending', 'inactive'])) {
            $query->where('status', $request->status);
        }
        
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
            
            // Handle expertise as JSON
            $data['expertise'] = json_encode($data['expertise']);
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentor-photos', 'public');
                $data['photo'] = $photoPath;
            }
            
            // Handle boolean checkboxes (they don't send value when unchecked)
            $data['notify_welcome'] = $request->has('notify_welcome');
            $data['notify_training'] = $request->has('notify_training');
            
            // Create the mentor
            $mentor = Mentor::create($data);
            
            // Send welcome email if requested
            if ($data['notify_welcome']) {
                // You can implement email sending here
                // Mail::to($mentor->email)->send(new MentorWelcomeMail($mentor));
            }
            
            return redirect()
                ->route('admin.mentors.index')
                ->with('success', 'Mentor created successfully!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create mentor: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to store a new mentor (for frontend AJAX submission)
     * This makes mentors visible to the frontend mentorship screen
     */
    public function storeApi(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mentors,email',
                'password' => 'required|string|min:12|confirmed',
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'bio' => 'required|string',
                'expertise_area' => 'required|string',
                'availability' => 'required|string',
                'available_days' => 'required|json',
                'available_time_start' => 'required|string',
                'available_time_end' => 'required|string',
                'status' => 'nullable|in:active,pending,inactive',
                'linkedin_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'website_url' => 'nullable|url',
                'notes' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Hash the password
            $validated['password'] = Hash::make($validated['password']);
            
            // Handle expertise (convert from comma-separated or array)
            if (isset($validated['expertise_area'])) {
                $validated['expertise'] = json_encode(explode(', ', $validated['expertise_area']));
            }
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentor-photos', 'public');
                $validated['photo'] = $photoPath;
            }
            
            // Set default values
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['notify_welcome'] = $request->has('notify_welcome');
            $validated['notify_training'] = $request->has('notify_training');
            
            // Create the mentor
            $mentor = Mentor::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Mentor created successfully and is now visible to users!',
                'data' => $mentor
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mentor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all mentors for API (frontend consumption)
     * This is the endpoint that the MentorshipScreen will call
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
                    $expertiseArray = json_decode($mentor->expertise, true);
                    $mentor->expertise_area = is_array($expertiseArray) ? implode(', ', $expertiseArray) : $mentor->expertise;
                }
                
                // Parse available_days if it's a JSON string
                if ($mentor->available_days && is_string($mentor->available_days)) {
                    $mentor->available_days = json_decode($mentor->available_days, true);
                }
                
                // Add full URL for avatar
                if ($mentor->avatar && !str_starts_with($mentor->avatar, 'http')) {
                    $mentor->avatar = asset('storage/' . $mentor->avatar);
                }
                
                return $mentor;
            });

        return response()->json($mentors);
    }

    /**
     * Get all mentors with pagination for admin API
     */
    public function getAllMentorsApi(Request $request)
    {
        $query = Mentor::query();
        
        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['active', 'pending', 'inactive'])) {
            $query->where('status', $request->status);
        }
        
        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $mentors = $query->latest()->paginate($request->get('per_page', 20));
        
        // Format expertise for each mentor
        $mentors->getCollection()->transform(function ($mentor) {
            if ($mentor->expertise) {
                $expertiseArray = json_decode($mentor->expertise, true);
                $mentor->expertise_area = is_array($expertiseArray) ? implode(', ', $expertiseArray) : $mentor->expertise;
            }
            return $mentor;
        });
        
        return response()->json([
            'success' => true,
            'data' => $mentors
        ]);
    }

    /**
     * Get a single mentor by ID (API)
     */
    public function showApi($id)
    {
        $mentor = Mentor::findOrFail($id);
        
        // Decode expertise
        if ($mentor->expertise) {
            $expertiseArray = json_decode($mentor->expertise, true);
            $mentor->expertise_area = is_array($expertiseArray) ? implode(', ', $expertiseArray) : $mentor->expertise;
        }
        
        // Parse available_days
        if ($mentor->available_days && is_string($mentor->available_days)) {
            $mentor->available_days = json_decode($mentor->available_days, true);
        }
        
        // Add full URL for photo
        if ($mentor->photo && !str_starts_with($mentor->photo, 'http')) {
            $mentor->photo_url = asset('storage/' . $mentor->photo);
        }
        
        return response()->json([
            'success' => true,
            'data' => $mentor
        ]);
    }

    /**
     * Update mentor status (API)
     */
    public function updateStatusApi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,pending,inactive'
        ]);
        
        $mentor = Mentor::findOrFail($id);
        $mentor->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Mentor status updated successfully',
            'data' => $mentor
        ]);
    }

    /**
     * Update mentor (API)
     */
    public function updateApi(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('mentors')->ignore($mentor->id)],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'sometimes|string',
            'expertise_area' => 'sometimes|string',
            'availability' => 'sometimes|string',
            'available_days' => 'sometimes|json',
            'available_time_start' => 'sometimes|string',
            'available_time_end' => 'sometimes|string',
            'status' => 'nullable|in:active,pending,inactive',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:12|confirmed'
            ]);
            $validated['password'] = Hash::make($request->password);
        }
        
        // Handle expertise
        if (isset($validated['expertise_area'])) {
            $validated['expertise'] = json_encode(explode(', ', $validated['expertise_area']));
            unset($validated['expertise_area']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $photoPath = $request->file('photo')->store('mentor-photos', 'public');
            $validated['photo'] = $photoPath;
        }
        
        // Handle available_days if it's an array
        if ($request->has('available_days') && is_array($request->available_days)) {
            $validated['available_days'] = json_encode($request->available_days);
        }

        $mentor->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Mentor updated successfully',
            'data' => $mentor
        ]);
    }

    /**
     * Delete mentor (API)
     */
    public function destroyApi($id)
    {
        $mentor = Mentor::findOrFail($id);
        
        // Delete photo if exists
        if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
            Storage::disk('public')->delete($mentor->photo);
        }
        
        $mentor->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Mentor deleted successfully'
        ]);
    }

    /**
     * Display the specified mentor (web view)
     */
    public function show(Mentor $mentor)
    {
        // Decode expertise for display
        if ($mentor->expertise) {
            $expertiseArray = json_decode($mentor->expertise, true);
            $mentor->expertise_list = is_array($expertiseArray) ? $expertiseArray : [];
        }
        
        // Parse available_days if it's a JSON string
        if ($mentor->available_days && is_string($mentor->available_days)) {
            $mentor->available_days_list = json_decode($mentor->available_days, true);
        }
        
        return view('admin.mentors.show', compact('mentor'));
    }

    /**
     * Show the form for editing the specified mentor
     */
    public function edit(Mentor $mentor)
    {
        // Decode expertise for form display
        if ($mentor->expertise) {
            $expertiseArray = json_decode($mentor->expertise, true);
            $mentor->expertise_array = is_array($expertiseArray) ? $expertiseArray : [];
        }
        
        // Parse available_days if it's a JSON string
        if ($mentor->available_days && is_string($mentor->available_days)) {
            $mentor->available_days_array = json_decode($mentor->available_days, true);
        }
        
        return view('admin.mentors.edit', compact('mentor'));
    }

    /**
     * Update the specified mentor in storage (web)
     */
    public function update(Request $request, Mentor $mentor)
    {
        // Similar validation as store but with unique email exception
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email,' . $mentor->id,
            'expertise' => 'required|array|min:1',
            'bio' => 'required|string|max:500',
            'status' => 'required|in:pending,active,inactive',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'availability' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $data['photo'] = $request->file('photo')->store('mentor-photos', 'public');
        }
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:12|confirmed'
            ]);
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        $data['expertise'] = json_encode($data['expertise']);
        $data['notify_welcome'] = $request->has('notify_welcome');
        $data['notify_training'] = $request->has('notify_training');
        
        $mentor->update($data);
        
        return redirect()
            ->route('admin.mentors.index')
            ->with('success', 'Mentor updated successfully!');
    }

    /**
     * Remove the specified mentor from storage (web)
     */
    public function destroy(Mentor $mentor)
    {
        // Delete photo if exists
        if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
            Storage::disk('public')->delete($mentor->photo);
        }
        
        $mentor->delete();
        
        return redirect()
            ->route('admin.mentors.index')
            ->with('success', 'Mentor deleted successfully!');
    }
}