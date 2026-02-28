<?php

namespace App\Http\Controllers;

use App\Models\HarassmentReport;
use Illuminate\Http\Request;

class HarassmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_type' => 'required|string',
            'description' => 'required|string|min:20',
            'location' => 'nullable|string',
            'incident_date' => 'nullable|date|before_or_equal:today',
            'perpetrator_info' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        $report = HarassmentReport::create([
            ...$validated,
            'user_id' => $validated['is_anonymous'] ? null : $request->user()?->id,
            'status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Your report has been submitted safely and confidentially. Our team will review it.',
            'report_id' => $report->id,
        ], 201);
    }

    public function myReports(Request $request)
    {
        $reports = HarassmentReport::where('user_id', $request->user()->id)
            ->select('id', 'incident_type', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }

    // Admin only
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reports = HarassmentReport::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reports);
    }

    // Admin: update status
    public function updateStatus(Request $request, HarassmentReport $report)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:under_review,resolved,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $report->update($validated);

        return response()->json(['message' => 'Report updated', 'report' => $report]);
    }
}