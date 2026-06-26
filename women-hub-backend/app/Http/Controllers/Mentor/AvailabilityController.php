<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    // public function index()
    // {
    //     $mentor = Auth::user();

    //     return view('mentor.settings.availability', compact('mentor'));
    // }


    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'mentor') {
            abort(403, 'Only mentors can view availability settings.');
        }

        return view('mentor.settings.Availability.index', [
            'available_days'       => $user->available_days ?? [],
            'available_time_start' => $user->available_time_start ?? '09:00',
            'available_time_end'   => $user->available_time_end ?? '17:00',
        ]);
    }

    /**
     * Update mentor availability.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'mentor') {
            abort(403, 'Only mentors can update availability.');
        }

        $validated = $request->validate([
            'available_days'      => 'required|array|min:1',
            'available_days.*'    => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'available_time_from' => 'required|date_format:H:i',
            'available_time_to'   => 'required|date_format:H:i|after:available_time_from',
        ]);

        $user->update([
            'available_days'       => $validated['available_days'],
            'available_time_start' => $validated['available_time_from'],
            'available_time_end'   => $validated['available_time_to'],
        ]);

        return redirect()
            ->route('mentor.availability')
            ->with('success', 'Availability updated successfully.');
    }
}



