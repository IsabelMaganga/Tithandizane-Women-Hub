<?php

namespace App\Http\Controllers\mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function Symfony\Component\String\s;

class SecurityController extends Controller
{

    public function showSettings(){

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.settings.index', compact(
            'mentorName',
            'mentorEmail'
        ));

    }

    public function showProfile(){

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
            'mentorAvailable'
        ));

    }

    public function updateProfile(Request $request, User $user){
        try {
            // Get current mentor user info
            $user = Auth::guard('mentor')->user();

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

            return redirect()->route('mentor.showProfile')
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
            'mentorAvailable'
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
