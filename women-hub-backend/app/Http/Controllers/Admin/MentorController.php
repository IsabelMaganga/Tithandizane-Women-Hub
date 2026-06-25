<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expertise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MentorController extends Controller
{
    /**
     * Base query — always scoped to role = 'mentor'.
     */
    private function mentorQuery()
    {
        return User::where('role', 'mentor');
    }

    // -------------------------------------------------------------------------
    // ADMIN CRUD
    // -------------------------------------------------------------------------

    /**
     * List mentors.
     * AJAX → full collection + stats as JSON.
     * Web  → paginated blade view.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $query = $this->mentorQuery();

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name',    'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('bio',   'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $mentors    = $query->with('expertises')->orderBy('created_at', 'desc')->get();
            $oneWeekAgo = Carbon::now()->subWeek();

            $stats = [
                'total'         => $this->mentorQuery()->count(),
                'active'        => $this->mentorQuery()->where('status', 'active')->count(),
                'pending'       => $this->mentorQuery()->where('status', 'pending')->count(),
                'inactive'      => $this->mentorQuery()->where('status', 'inactive')->count(),
                'new_this_week' => $this->mentorQuery()->where('created_at', '>=', $oneWeekAgo)->count(),
            ];

            return response()->json([
                'success' => true,
                'mentors' => $mentors,
                'stats'   => $stats,
                'total'   => $mentors->count(),
            ]);
        }

        // Normal web request — paginated
        $mentors = $this->mentorQuery()->with('expertises')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.mentors.index', compact('mentors'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $adminName  = Auth::guard('admin')->user()->name  ?? 'Admin User';
        $adminEmail = Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org';

        return view('admin.mentors.create', compact('adminName', 'adminEmail'));
    }

    /**
     * Store a new mentor.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'                 => 'required|string|max:255',
                'email'                => 'required|email|unique:users,email',
                'phone'                => 'nullable|string|max:20',
                'password'             => 'required|string|min:8|confirmed',
                'location'             => 'nullable|string|max:255',
                'photo'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'expertise'            => 'nullable|array',
                'expertise.*'          => 'string|max:100',
                'bio'                  => 'required|string',
                'availability'         => 'nullable|string',
                'available_days'       => 'nullable|array',
                'available_days.*'     => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'available_time_start' => 'nullable|date_format:H:i',
                'available_time_end'   => 'nullable|date_format:H:i|after:available_time_start',
                'linkedin_url'         => 'nullable|url',
                'twitter_url'          => 'nullable|url',
                'website_url'          => 'nullable|url',
                'notes'                => 'nullable|string',
                'status'               => 'sometimes|string|in:active,pending,inactive',
                'notify_welcome'       => 'nullable|boolean',
                'notify_training'      => 'nullable|boolean',
            ]);

            // ── Pull out expertise before creating the user ──────────────────
            $expertiseNames = $validated['expertise'] ?? [];
            unset($validated['expertise']);

            // ── Pull out fields not in users table ───────────────────────────
            unset($validated['password_confirmation']);
            unset($validated['notify_welcome']);
            unset($validated['notify_training']);

            // ── Photo upload ─────────────────────────────────────────────────
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('mentors', 'public');
            }

            // ── Hash password ────────────────────────────────────────────────
            $validated['password'] = Hash::make($validated['password']);

            // ── JSON encode available_days ───────────────────────────────────
            if (isset($validated['available_days']) && is_array($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            }

            // ── Force role and default status ────────────────────────────────
            $validated['role']   = 'mentor';
            $validated['status'] = $validated['status'] ?? 'pending';

            // ── Create user ──────────────────────────────────────────────────
            $mentor = User::create($validated);

            // ── Attach expertise via pivot table ─────────────────────────────
            if (!empty($expertiseNames)) {
                $expertiseIds = [];
                foreach ($expertiseNames as $name) {
                    $name = trim($name);
                    if ($name === '') continue;

                    $expertise = Expertise::firstOrCreate(
                        ['name' => $name],
                        ['slug' => Str::slug($name)]
                    );
                    $expertiseIds[] = $expertise->id;
                }
                $mentor->expertises()->sync($expertiseIds);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor created successfully',
                    'mentor'  => $mentor->load('expertises'),
                ]);
            }

            return redirect()->route('admin.mentors.index')->with('success', 'Mentor created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            \Log::error('MentorController@store: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create mentor: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Failed to create mentor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show a single mentor (admin view).
     */
    public function show($id)
    {
        $mentor = $this->mentorQuery()->with('expertises')->findOrFail($id);
        $this->decodeJsonFields($mentor);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'mentor' => $mentor]);
        }

        return view('admin.mentors.show', compact('mentor'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $mentor = $this->mentorQuery()->with('expertises')->findOrFail($id);
        $this->decodeJsonFields($mentor);

        $adminName  = Auth::guard('admin')->user()->name  ?? 'Admin User';
        $adminEmail = Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org';

        return view('admin.mentors.edit', compact('mentor', 'adminName', 'adminEmail'));
    }

    /**
     * Update a mentor.
     */
    public function update(Request $request, $id)
    {
        try {
            $mentor = $this->mentorQuery()->findOrFail($id);

            $validated = $request->validate([
                'name'                 => 'required|string|max:255',
                'email'                => 'required|email|unique:users,email,' . $id,
                'phone'                => 'nullable|string|max:20',
                'location'             => 'nullable|string|max:255',
                'photo'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'expertise'            => 'nullable|array',
                'expertise.*'          => 'string|max:100',
                'bio'                  => 'required|string',
                'availability'         => 'nullable|string',
                'available_days'       => 'nullable|array',
                'available_days.*'     => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'available_time_start' => 'nullable|date_format:H:i',
                'available_time_end'   => 'nullable|date_format:H:i|after:available_time_start',
                'linkedin_url'         => 'nullable|url',
                'twitter_url'          => 'nullable|url',
                'website_url'          => 'nullable|url',
                'notes'                => 'nullable|string',
                'status'               => 'sometimes|string|in:active,pending,inactive',
            ]);

            // ── Pull out expertise before updating ───────────────────────────
            $expertiseNames = $validated['expertise'] ?? null;
            unset($validated['expertise']);

            // ── Photo upload ─────────────────────────────────────────────────
            if ($request->hasFile('photo')) {
                if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                    Storage::disk('public')->delete($mentor->photo);
                }
                $validated['photo'] = $request->file('photo')->store('mentors', 'public');
            }

            // ── JSON encode available_days ───────────────────────────────────
            if (isset($validated['available_days']) && is_array($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            } else {
                // Keep existing value if not submitted
                unset($validated['available_days']);
            }

            // Never allow role to change via update
            unset($validated['role']);

            $mentor->update($validated);

            // ── Sync expertise pivot ─────────────────────────────────────────
            if ($expertiseNames !== null) {
                $expertiseIds = [];
                foreach ($expertiseNames as $name) {
                    $name = trim($name);
                    if ($name === '') continue;

                    $expertise = Expertise::firstOrCreate(
                        ['name' => $name],
                        ['slug' => Str::slug($name)]
                    );
                    $expertiseIds[] = $expertise->id;
                }
                $mentor->expertises()->sync($expertiseIds);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mentor updated successfully',
                    'mentor'  => $mentor->load('expertises'),
                ]);
            }

            return redirect()->route('admin.mentors.index')->with('success', 'Mentor updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            \Log::error('MentorController@update: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update mentor: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withErrors(['error' => 'Failed to update mentor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a mentor.
     */
    public function destroy($id)
    {
        try {
            $mentor = $this->mentorQuery()->findOrFail($id);

            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }

            // Detach expertise pivot rows first (cascade should handle it, but being explicit)
            $mentor->expertises()->detach();
            $mentor->delete();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Mentor deleted successfully']);
            }

            return redirect()->route('admin.mentors.index')->with('success', 'Mentor deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('MentorController@destroy: ' . $e->getMessage());
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete mentor: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Failed to delete mentor: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle active / inactive status.
     */
    public function toggleStatus($id)
    {
        try {
            $mentor         = $this->mentorQuery()->findOrFail($id);
            $mentor->status = $mentor->status === 'active' ? 'inactive' : 'active';
            $mentor->save();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Status updated', 'status' => $mentor->status]);
            }

            return back()->with('success', 'Mentor status updated successfully.');

        } catch (\Exception $e) {
            \Log::error('MentorController@toggleStatus: ' . $e->getMessage());
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // STATS ENDPOINT
    // -------------------------------------------------------------------------

    public function getMentorStats(Request $request)
    {
        try {
            $oneWeekAgo = Carbon::now()->subWeek();

            $stats = [
                'total'          => $this->mentorQuery()->count(),
                'active'         => $this->mentorQuery()->where('status', 'active')->count(),
                'pending'        => $this->mentorQuery()->where('status', 'pending')->count(),
                'inactive'       => $this->mentorQuery()->where('status', 'inactive')->count(),
                'new_this_week'  => $this->mentorQuery()->where('created_at', '>=', $oneWeekAgo)->count(),
                'new_this_month' => $this->mentorQuery()
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at',  now()->year)
                                        ->count(),
                'total_sessions' => $this->mentorQuery()->sum('total_sessions') ?? 0,
                'avg_rating'     => round($this->mentorQuery()->whereNotNull('rating')->avg('rating') ?? 0, 1),
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'stats' => $stats]);
            }

            return $stats;

        } catch (\Exception $e) {
            \Log::error('MentorController@getMentorStats: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to fetch stats'], 500);
            }
            return [];
        }
    }

    // -------------------------------------------------------------------------
    // MOBILE / FRONTEND API ENDPOINTS
    // -------------------------------------------------------------------------

    public function getActiveMentors(Request $request)
    {
        try {
            $query = $this->mentorQuery()->with('expertises')->where('status', 'active')->orderBy('name');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('bio',  'like', "%{$search}%");
                });
            }

            if ($request->filled('expertise')) {
                $expertiseName = $request->expertise;
                $query->whereHas('expertises', function ($q) use ($expertiseName) {
                    $q->where('name', 'like', "%{$expertiseName}%");
                });
            }

            $mentors = $query->get()->map(fn($m) => $this->formatForApi($m));

            return response()->json(['success' => true, 'mentors' => $mentors, 'total' => $mentors->count()]);

        } catch (\Exception $e) {
            \Log::error('MentorController@getActiveMentors: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch mentors: ' . $e->getMessage(), 'mentors' => []], 500);
        }
    }

    public function getMentorDetails($id)
    {
        try {
            $mentor = $this->mentorQuery()->with('expertises')->where('status', 'active')->findOrFail($id);

            return response()->json(['success' => true, 'mentor' => $this->formatForApi($mentor, true)]);

        } catch (\Exception $e) {
            \Log::error('MentorController@getMentorDetails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Mentor not found'], 404);
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Decode JSON fields (available_days) on a User instance.
     * Expertise is now loaded via the relationship, not a JSON column.
     */
    private function decodeJsonFields(User $mentor): User
    {
        $mentor->available_days = is_string($mentor->available_days)
            ? (json_decode($mentor->available_days, true) ?? [])
            : (is_array($mentor->available_days) ? $mentor->available_days : []);

        return $mentor;
    }

    /**
     * Format a mentor for the mobile API response.
     */
    private function formatForApi(User $mentor, bool $includeNotes = false): array
    {
        // expertises is a relationship collection now
        $expertise = $mentor->relationLoaded('expertises')
            ? $mentor->expertises->pluck('name')->toArray()
            : $mentor->expertises()->pluck('name')->toArray();

        $availableDays = is_string($mentor->available_days)
            ? (json_decode($mentor->available_days, true) ?? [])
            : (is_array($mentor->available_days) ? $mentor->available_days : []);

        $data = [
            'id'                   => $mentor->id,
            'name'                 => $mentor->name,
            'email'                => $mentor->email,
            'phone'                => $mentor->phone,
            'location'             => $mentor->location,
            'photo'                => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
            'avatar'               => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
            'expertise'            => $expertise,
            'bio'                  => $mentor->bio ?? '',
            'availability'         => $mentor->availability,
            'available_days'       => $availableDays,
            'available_time_start' => $mentor->available_time_start,
            'available_time_end'   => $mentor->available_time_end,
            'linkedin_url'         => $mentor->linkedin_url,
            'twitter_url'          => $mentor->twitter_url,
            'website_url'          => $mentor->website_url,
            'rating'               => $mentor->rating         ?? null,
            'total_sessions'       => $mentor->total_sessions ?? 0,
            'status'               => $mentor->status,
            'role'                 => $mentor->role,
            'created_at'           => $mentor->created_at,
        ];

        if ($includeNotes) {
            $data['notes'] = $mentor->notes;
        }

        return $data;
    }
}