<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = Mentor::latest()->paginate(10);
        return view('admin.mentors.index', compact('mentors'));
    }

    public function create()
    {
        return view('admin.addmentor', [
            'adminName' => auth()->user()->name,
            'adminEmail' => auth()->user()->email
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'required|string',
            'expertise' => 'required|array|min:1',
            'status' => 'required|in:active,pending,inactive',
            'availability' => 'nullable|string',
        ]);

        // Map expertise array to area_of_support
        $areaOfSupport = 'both';
        if (count($request->expertise) === 1) {
            $areaOfSupport = $request->expertise[0];
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'area_of_support' => $areaOfSupport,
            'status' => $request->status,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('mentors', 'public');
        }

        Mentor::create($data);

        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors,email,' . $mentor->id,
            'expertise' => 'required|array',
            'availability' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive'
        ]);

        $mentor->update([
            'name' => $request->name,
            'email' => $request->email,
            'expertise' => $request->expertise,
            'availability' => $request->availability,
            'status' => $request->status
        ]);

        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor updated successfully.');
    }

    public function destroy(Mentor $mentor)
    {
        $mentor->delete();
        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor deleted successfully.');
    }

    public function toggleStatus(Mentor $mentor)
    {
        $newStatus = $mentor->status === 'active' ? 'inactive' : 'active';
        $mentor->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', "Mentor status changed to {$newStatus}.");
    }
}
