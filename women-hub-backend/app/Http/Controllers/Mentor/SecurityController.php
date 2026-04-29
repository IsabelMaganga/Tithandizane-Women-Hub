<?php

namespace App\Http\Controllers\mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Log, Storage};
use App\Http\Controllers\Controller;
use App\Models\{EmergencyContact, GeneralGuide, HygieneArticle, User};
use Illuminate\View\View;

class SecurityController extends Controller
{

    // showCalender controller
    public function showCalender(){

        // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.calender.index', compact(
            'mentorName',
            'mentorEmail',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    // showAppointments controller
    public function showAppointments(){

        // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.appointments.index', compact(
            'mentorName',
            'mentorEmail',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    // chat tab controller
    public function showChat(){

         // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.chat.index', compact(
            'mentorName',
            'mentorEmail',
        ));

    }

    // chat tab controller
    public function showChatGroups(){

         // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.chat.groups', compact(
            'mentorName',
            'mentorEmail',
        ));

    }

    public function showGroupForm(){

         // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.chat.create-group', compact(
            'mentorName',
            'mentorEmail',
             'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    // profile tab controller
    public function showMyProfile(){

          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $mentorPhone = $mentorUser->phone;
        $mentorBio = $mentorUser->bio;
        $mentorCreatedAt = $mentorUser->created_at->format('l, d F Y');
        $mentorAvailable = $mentorUser->is_available;


        return view('mentor.profile.index', compact(
            'mentorName',
            'mentorPhone',
            'mentorEmail',
            'mentorUser',
            'mentorBio',
            'mentorCreatedAt',
            'mentorAvailable',
             'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    // guidance controllers
    public function showGuidance(){

          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.guidance.index', compact(
            'mentorName',
            'mentorEmail',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    public function showHygiene(){

          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $hygiene = HygieneArticle::all();
        $hygieneCreatedAt = $mentorUser->updated_at->format('d F Y');

        return view('mentor.guidance.hygiene.index', compact(
            'mentorName',
            'mentorEmail',
            'mentorUser',
            'hygiene',
            'hygieneCreatedAt',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    public function showEmergency(){


          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $contact = EmergencyContact::all();


        return view('mentor.guidance.emergency.index', compact(
            'mentorName',
            'mentorEmail',
            'mentorUser',
            'contact',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    public function showGeneral(){


          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $general = GeneralGuide::all();
        $generalCreatedAt = $mentorUser->updated_at->format('d F Y');


        return view('mentor.guidance.general.index', compact(
            'mentorName',
            'mentorEmail',
            'mentorUser',
            'general',
            'generalCreatedAt',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    // settings related controllers below
    public function showSettings(){


          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.settings.index', compact(
            'mentorName',
            'mentorEmail',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    public function showProfile(){


          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $mentorPhone = $mentorUser->phone;
        $mentorBio = $mentorUser->bio;
        $mentorCreatedAt = $mentorUser->created_at->format('l, d F Y');
        $mentorAvailable = $mentorUser->is_available;


        return view('mentor.settings.profile.index', compact(
            'mentorName',
            'mentorPhone',
            'mentorEmail',
            'mentorUser',
            'mentorBio',
            'mentorCreatedAt',
            'mentorAvailable',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

    public function updateProfile(Request $request, User $user){
        try {
            // Get current mentor user info
            $user = Auth::guard('usersl')->user();


            if (!$user) {
                return back()->withErrors(['error' => 'No mentor is logged in.']);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'bio' => 'required|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle profile picture upload
            if ($request->hasFile("profile_picture")) {
                // Delete old image if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Store new image with unique name to prevent caching issues
                $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                $validated['profile_picture'] = $imagePath;
            }

            // Update user with validated data
            $user->update($validated);


            return view('mentor.showProfile', compact('user'))
                ->with('success', 'Profile updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            // Log error with more details for debugging
            Log::error("Profile update failed for user ID: " . ($user->id ?? 'unknown') . " | Error: " . $e->getMessage());

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }

    public function showSecurity(){


          // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $mentorPasswordUpdatedDate = $mentorUser->updated_at->format('d F Y');
        $mentorPasswordUpdatedTime = $mentorUser->updated_at->format('H:i:s A');
        $mentorAvailable = $mentorUser->is_available;

        return view('mentor.settings.security.index', compact(
            'mentorName',
            'mentorEmail',
            'mentorPasswordUpdatedDate',
            'mentorPasswordUpdatedTime',
            'mentorAvailable',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));

    }

     public function updateSecurity(Request $request, User $user){
        try {
            // Get current mentor user info
            $user = Auth::guard('mentor')->user();

            $validated = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|confirmed|min:8|max:255',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                 return back()->withErrors([
                    'current_password' => 'current password is incorrect.'
                ]);
            }

            // Update user with validated data
            $user->update([
                'password' => Hash::make($request->current_password)
            ]);

            return redirect()->route('mentor.showSecurity')
                ->with('success', 'Password updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            // Log error with more details for debugging
            Log::error("password update failed for user ID: " . ($user->id ?? 'unknown') . " | Error: " . $e->getMessage());

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }


}
