<?php

namespace App\Http\Controllers\mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\EmergencyContact;
use App\Models\GeneralGuide;
use App\Models\HygieneArticle;
use App\Models\MentorshipSession;

class SecurityController extends Controller
{
    /**
     * Get mentor + notifications (GLOBAL HELPER)
     */
    private function baseData()
    {
        $mentor = Auth::guard('mentor')->user();

        if (!$mentor) {
            abort(403, 'Unauthorized');
        }

        return [
            'mentorUser' => $mentor,
            'mentorName' => $mentor->name,
            'mentorEmail' => $mentor->email,

            'notifications' => $mentor->notifications()->latest()->get(),
            'unreadCount' => $mentor->unreadNotifications()->count(),
            'unreadNotifications' => $mentor->unreadNotifications()->paginate(5),
        ];
    }

    /* =========================
        DASHBOARD PAGES
    ========================= */

    public function showCalender()
    {
        return view('mentor.calender.index', $this->baseData());
    }

   

public function showAppointments()
{
    $mentor = Auth::guard('mentor')->user();

    return view('mentor.appointments.index', [
        'incomingSessions' => MentorshipSession::where('mentor_id', $mentor->id)
            ->where('status', 'accepted')
            ->with('mentee')
            ->latest()
            ->get(),

        'missedSessions' => MentorshipSession::where('mentor_id', $mentor->id)
            ->where('status', 'missed')
            ->with('mentee')
            ->latest()
            ->get(),

        'unattendedSessions' => MentorshipSession::where('mentor_id', $mentor->id)
            ->where('status', 'pending')
            ->with('mentee')
            ->latest()
            ->get(),
    ] + $this->baseData());
}

    public function showChat()
    {
        return view('mentor.chat.index', $this->baseData());
    }

    public function showChatGroups()
    {
        return view('mentor.chat.groups', $this->baseData());
    }

    public function showGroupForm()
    {
        return view('mentor.chat.create-group', $this->baseData());
    }

    /* =========================
        PROFILE
    ========================= */

    public function showMyProfile()
    {
        $data = $this->baseData();
        $mentor = $data['mentorUser'];

        $data['mentorPhone'] = $mentor->phone;
        $data['mentorBio'] = $mentor->bio;
        $data['mentorCreatedAt'] = $mentor->created_at->format('l, d F Y');
        $data['mentorAvailable'] = $mentor->is_available;

        return view('mentor.profile.index', $data);
    }

    /* =========================
        GUIDANCE
    ========================= */

    public function showGuidance()
    {
        return view('mentor.guidance.index', $this->baseData());
    }

    public function showHygiene()
    {
        $data = $this->baseData();

        $data['hygiene'] = HygieneArticle::all();

        return view('mentor.guidance.hygiene.index', $data);
    }

    public function showEmergency()
    {
        $data = $this->baseData();

        $data['contact'] = EmergencyContact::all();

        return view('mentor.guidance.emergency.index', $data);
    }

    public function showGeneral()
    {
        $data = $this->baseData();

        $data['general'] = GeneralGuide::all();

        return view('mentor.guidance.general.index', $data);
    }

    /* =========================
        SETTINGS
    ========================= */

    public function showSettings()
    {
        return view('mentor.settings.index', $this->baseData());
    }

    public function showProfile()
    {
        return view('mentor.settings.profile.index', $this->baseData());
    }

    public function showSecurity()
    {
        $data = $this->baseData();
        $mentor = $data['mentorUser'];

        $data['mentorPasswordUpdatedDate'] = $mentor->updated_at->format('d F Y');
        $data['mentorPasswordUpdatedTime'] = $mentor->updated_at->format('H:i:s A');

        return view('mentor.settings.security.index', $data);
    }

    /* =========================
        UPDATE PROFILE
    ========================= */

    public function updateProfile(Request $request)
    {
        $mentor = Auth::guard('mentor')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'bio' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {

            if ($mentor->profile_picture && Storage::disk('public')->exists($mentor->profile_picture)) {
                Storage::disk('public')->delete($mentor->profile_picture);
            }

            $validated['profile_picture'] =
                $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $mentor->update($validated);

        return redirect()
            ->route('mentor.showProfile')
            ->with('success', 'Profile updated successfully.');
    }

    /* =========================
        UPDATE PASSWORD
    ========================= */

    public function updateSecurity(Request $request)
    {
        $mentor = Auth::guard('mentor')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, $mentor->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        $mentor->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()
            ->route('mentor.showSecurity')
            ->with('success', 'Password updated successfully.');
    }
}