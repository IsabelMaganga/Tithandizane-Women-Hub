<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Event;

class CalenderController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('mentor.calender.index', compact('events'));
    }

    public function showCalender()
    {
        return $this->index();
    }

    // Optional: Fetch events as JSON for AJAX (better performance)
    public function getEvents()
    {
        $events = Event::all();
        return response()->json($events);
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'title' => 'required|string|max:255',
                'start' => 'required|date',
                'end' => 'nullable|date'
            ]);

            $event = Event::create([
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end ?? $request->start,
                'description' => $request->description,
                'color' => $request->color ?? '#3788d8'
            ]);

            return response()->json($event, 201);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();
            return response()->json([
                'message' => 'Event deleted successfully'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
