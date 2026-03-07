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
        return view('admin.mentors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:mentors',
            'expertise' => 'required|array',
            'availability' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive'
        ]);

        Mentor::create([
            'name' => $request->name,
            'email' => $request->email,
            'expertise' => $request->expertise,
            'availability' => $request->availability,
            'status' => $request->status
        ]);

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
