<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MentorStoreRequest;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = Mentor::latest()->paginate(10);
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

    public function show(Mentor $mentor)
    {
        return view('admin.mentors.show', compact('mentor'));
    }

    public function edit(Mentor $mentor)
    {
        return view('admin.mentors.edit', compact('mentor'));
    }

    public function update(Request $request, Mentor $mentor)
    {
        // Similar validation as store but with unique email exception
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email,' . $mentor->id,
            'expertise' => 'required|array|min:1',
            'bio' => 'required|string|max:500',
            'status' => 'required|in:pending,active,inactive',
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