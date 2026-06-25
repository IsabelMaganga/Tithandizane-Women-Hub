<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display the events calendar
     */
    public function index()
    {
        $events = Event::with('creator')->orderBy('start_date', 'asc')->get();
        
        return view('admin.events.index', compact('events'));
    }

    /**
     * Get events as JSON for calendar
     */
    public function getEvents(Request $request)
    {
        $query = Event::query();
        
        // Filter by date range if provided
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_date', [$request->start, $request->end]);
        }
        
        $events = $query->get()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date . 'T' . ($event->start_time ?? '00:00'),
                'end' => $event->end_date ? $event->end_date . 'T' . ($event->end_time ?? '23:59') : $event->start_date . 'T' . ($event->end_time ?? '23:59'),
                'color' => $event->color,
                'description' => $event->description,
                'location' => $event->location,
                'type' => $event->type,
                'status' => $event->status,
                'extendedProps' => [
                    'location' => $event->location,
                    'type' => $event->type,
                    'status' => $event->status,
                    'max_participants' => $event->max_participants,
                    'current_participants' => $event->current_participants,
                ]
            ];
        });
        
        return response()->json($events);
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:training,workshop,meeting,general',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'color' => 'required|string|max:7',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $validated['created_by'] = Auth::guard('admin')->id();
        $validated['current_participants'] = 0;

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:training,workshop,meeting,general',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'color' => 'required|string|max:7',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $event->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
