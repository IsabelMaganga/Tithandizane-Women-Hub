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
            'phone' => 'nullable|string',
            'bio' => 'required|string|min:10',
            'expertise' => 'required|array|min:1',
            'available_days' => 'required|array|min:1',
            'available_time_from' => 'required|string',
            'available_time_to' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        // Convert expertise array to area_of_support
        $areaOfSupport = 'both';
        if (count($request->expertise) === 1) {
            $areaOfSupport = $request->expertise[0];
        }

        Mentor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'area_of_support' => $areaOfSupport,
            'available_days' => $request->available_days,
            'available_time_from' => $request->available_time_from,
            'available_time_to' => $request->available_time_to,
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
            'phone' => 'nullable|string',
            'bio' => 'required|string|min:10',
            'expertise' => 'required|array|min:1',
            'available_days' => 'required|array|min:1',
            'available_time_from' => 'required|string',
            'available_time_to' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        // Convert expertise array to area_of_support
        $areaOfSupport = 'both';
        if (count($request->expertise) === 1) {
            $areaOfSupport = $request->expertise[0];
        }

        $mentor->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'area_of_support' => $areaOfSupport,
            'available_days' => $request->available_days,
            'available_time_from' => $request->available_time_from,
            'available_time_to' => $request->available_time_to,
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
