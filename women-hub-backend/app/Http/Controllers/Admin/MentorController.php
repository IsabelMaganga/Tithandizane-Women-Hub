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
     * Get active mentors for frontend display (React Native)
     */
    public function getActiveMentors(Request $request)
    {
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
        
        // Format mentors for frontend
        $formattedMentors = $mentors->map(function($mentor) {
            return [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'email' => $mentor->email,
                'phone' => $mentor->phone,
                'location' => $mentor->location,
                'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                'expertise' => is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise,
                'bio' => $mentor->bio,
                'availability' => $mentor->availability,
                'available_days' => $mentor->available_days ? (is_string($mentor->available_days) ? json_decode($mentor->available_days, true) : $mentor->available_days) : [],
                'available_time_start' => $mentor->available_time_start,
                'available_time_end' => $mentor->available_time_end,
                'linkedin_url' => $mentor->linkedin_url,
                'twitter_url' => $mentor->twitter_url,
                'website_url' => $mentor->website_url,
                'rating' => $mentor->rating ?? null,
                'total_sessions' => $mentor->total_sessions ?? 0,
            ];
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Mentors retrieved successfully',
            'mentors' => $formattedMentors,
            'total' => $formattedMentors->count()
        ]);
    }
    
    /**
     * Get a single mentor details for frontend
     */
    public function getMentorDetails($id)
    {
        $mentor = Mentor::where('id', $id)
            ->where('status', 'active')
            ->first();
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor not found'
            ], 404);
        }
        
        $formattedMentor = [
            'id' => $mentor->id,
            'name' => $mentor->name,
            'email' => $mentor->email,
            'phone' => $mentor->phone,
            'location' => $mentor->location,
            'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
            'expertise' => is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise,
            'bio' => $mentor->bio,
            'availability' => $mentor->availability,
            'available_days' => $mentor->available_days ? (is_string($mentor->available_days) ? json_decode($mentor->available_days, true) : $mentor->available_days) : [],
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
    }
    
    /**
     * Get mentor statistics for dashboard
     */
    public function getMentorStats()
    {
        $stats = [
            'total' => Mentor::count(),
            'active' => Mentor::where('status', 'active')->count(),
            'pending' => Mentor::where('status', 'pending')->count(),
            'inactive' => Mentor::where('status', 'inactive')->count(),
            'expertise_areas' => $this->getExpertiseStats(),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
    
    /**
     * Get expertise statistics
     */
    private function getExpertiseStats()
    {
        $mentors = Mentor::where('status', 'active')->get();
        $expertiseCount = [];
        
        foreach ($mentors as $mentor) {
            $expertise = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
            if (is_array($expertise)) {
                foreach ($expertise as $area) {
                    if (!isset($expertiseCount[$area])) {
                        $expertiseCount[$area] = 0;
                    }
                    $expertiseCount[$area]++;
                }
            }
        }
        
        arsort($expertiseCount);
        return array_slice($expertiseCount, 0, 10, true);
    }

    // Your existing methods (index, create, store, show, edit, update, destroy, toggleStatus)
    // ... (keep all your existing code below)
}