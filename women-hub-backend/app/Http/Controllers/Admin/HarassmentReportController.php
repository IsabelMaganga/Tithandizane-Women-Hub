<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HarassmentReport;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HarassmentReportController extends Controller
{
   
    public function index(Request $request)
    {
        try {
            $query = HarassmentReport::with(['assignedMentor', 'user']);
            
            // Apply search filter
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('incident_title', 'like', "%{$searchTerm}%")
                      ->orWhere('incident_description', 'like', "%{$searchTerm}%")
                      ->orWhere('reference_number', 'like', "%{$searchTerm}%")
                      ->orWhere('victim_name', 'like', "%{$searchTerm}%")
                      ->orWhere('victim_email', 'like', "%{$searchTerm}%");
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply type filter
            if ($request->filled('type')) {
                $query->where('incident_type', $request->type);
            }

            // Apply date range filters
            if ($request->filled('from_date')) {
                $query->whereDate('incident_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('incident_date', '<=', $request->to_date);
            }

            // Apply anonymous filter
            if ($request->has('anonymous')) {
                $query->where('is_anonymous', $request->anonymous === 'true');
            }

            // Custom ordering using CASE statement (works with SQLite and MySQL)
            $query->orderByRaw("
                CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'reviewing' THEN 2 
                    WHEN 'assigned' THEN 3 
                    WHEN 'resolved' THEN 4 
                    WHEN 'dismissed' THEN 5 
                    ELSE 6 
                END
            ");
            $query->orderBy('created_at', 'desc');

            $reports = $query->paginate(15);
            
            // Load statistics
            $stats = [
                'total' => HarassmentReport::count(),
                'pending' => HarassmentReport::where('status', 'pending')->count(),
                'reviewing' => HarassmentReport::where('status', 'reviewing')->count(),
                'assigned' => HarassmentReport::where('status', 'assigned')->count(),
                'resolved' => HarassmentReport::where('status', 'resolved')->count(),
                'dismissed' => HarassmentReport::where('status', 'dismissed')->count(),
                'anonymous' => HarassmentReport::where('is_anonymous', true)->count(),
            ];

            // For AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'reports' => $reports,
                    'stats' => $stats
                ]);
            }

            return view('admin.reports.index', compact('reports', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading reports: ' . $e->getMessage());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load reports: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to load reports: ' . $e->getMessage());
        }
    }

    //display reports
    public function show($id)
    {
        try {
            $report = HarassmentReport::with(['assignedMentor', 'user'])->findOrFail($id);
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $report
                ]);
            }
            
            $mentors = User::where('role', 'mentor')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
                
            return view('admin.reports.show', compact('report', 'mentors'));
        } catch (\Exception $e) {
            Log::error('Error showing report: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }
            
            return redirect()->route('admin.reports.index')->with('error', 'Report not found');
        }
    }

    // Submit a new harassment report (used by the mobile app — both anonymous and identified)
    public function store(Request $request)
    {
        try {
            $isAnonymous = filter_var($request->input('is_anonymous'), FILTER_VALIDATE_BOOLEAN);

            $rules = [
                'incident_type'        => 'required|in:physical,verbal,sexual,cyber,other',
                'incident_title'       => 'required|string|min:3|max:255',
                'incident_description' => 'required|string|min:10',
                'incident_location'    => 'required|string|max:255',
                'incident_date'        => 'required|date',
                'perpetrator_info'     => 'nullable|string|max:1000',
                'is_anonymous'         => 'required',
            ];

            if (!$isAnonymous) {
                $rules['victim_name']  = 'required|string|max:255';
                $rules['victim_email'] = 'required|email|max:255';
                $rules['victim_phone'] = 'nullable|string|max:20';
            }

            $validated = $request->validate($rules);

            $report = HarassmentReport::create([
                'incident_type'        => $validated['incident_type'],
                'incident_title'       => $validated['incident_title'],
                'incident_description' => $validated['incident_description'],
                'incident_location'    => $validated['incident_location'],
                'incident_date'        => $validated['incident_date'],
                'perpetrator_info'     => $validated['perpetrator_info'] ?? null,
                'is_anonymous'         => $isAnonymous,
                'victim_name'          => $isAnonymous ? null : ($validated['victim_name'] ?? null),
                'victim_email'         => $isAnonymous ? null : ($validated['victim_email'] ?? null),
                'victim_phone'         => $isAnonymous ? null : ($validated['victim_phone'] ?? null),
                'status'               => 'pending',
                'user_id'              => Auth::id() ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully.',
                'data'    => [
                    'id'               => $report->id,
                    'reference_number' => $report->reference_number,
                    'status'           => $report->status,
                    'is_anonymous'     => $report->is_anonymous,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to submit harassment report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.',
            ], 500);
        }
    }

    // Legacy route aliases — kept for backward compatibility with non-v1 API routes
    public function submitReport(Request $request)   { return $this->store($request); }
    public function submitAnonymousReport(Request $request) { return $this->store($request); }

    //assign mentor
    public function assignMentor(Request $request, $id)
    {
        $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $report = HarassmentReport::findOrFail($id);
            $mentor = User::findOrFail($request->mentor_id);

            // Check if report can be assigned
            if (!$report->canBeAssigned()) {
                throw new \Exception('This report cannot be assigned in its current status.');
            }
            
            $oldStatus = $report->status;
            $oldMentorId = $report->assigned_mentor_id;
            
            $report->update([
                'assigned_mentor_id' => $mentor->id,
                'status' => 'assigned'
            ]);

            // Create notification for the new mentor
            Notification::create([
                'type' => 'report_assigned',
                'user_id' => $mentor->id,
                'report_id' => $report->id,
                'title' => 'New Report Assigned to You',
                'message' => "A harassment report has been assigned to you for handling. Reference: {$report->reference_number}",
                'data' => [
                    'report_id' => $report->id,
                    'reference_number' => $report->reference_number,
                    'incident_type' => $report->incident_type,
                    'notes' => $request->notes,
                    'assigned_by' => Auth::guard('admin')->user()?->name ?? 'Admin'
                ]
            ]);

            // Notify the previous mentor if reassigned
            if ($oldMentorId && $oldMentorId != $mentor->id) {
                Notification::create([
                    'type' => 'report_unassigned',
                    'user_id' => $oldMentorId,
                    'report_id' => $report->id,
                    'title' => 'Report Reassigned',
                    'message' => "Report {$report->reference_number} has been reassigned to another mentor.",
                    'data' => [
                        'report_id' => $report->id,
                        'reference_number' => $report->reference_number
                    ]
                ]);
            }

            // If report is not anonymous, notify the victim via email
            if (!$report->is_anonymous && $report->victim_email) {
                $this->sendAssignmentNotificationToVictim($report, $mentor);
            }

            DB::commit();

            $message = $oldMentorId ? 'Mentor reassigned successfully' : 'Mentor assigned successfully';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $report->fresh(['assignedMentor'])
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign mentor: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign mentor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to assign mentor: ' . $e->getMessage());
        }
    }

    //respond to report
    public function respondToReport(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|min:10|max:5000',
            'status' => 'required|in:reviewing,resolved,dismissed'
        ]);

        try {
            DB::beginTransaction();
            
            $report = HarassmentReport::findOrFail($id);
            
            $oldStatus = $report->status;
            
            $report->update([
                'admin_response' => $request->response,
                'status' => $request->status,
                'responded_at' => now()
            ]);

            // If not anonymous, send response to victim via email
            if (!$report->is_anonymous && $report->victim_email) {
                $this->sendResponseToVictim($report);
            }

            // Notify assigned mentor if any
            if ($report->assigned_mentor_id) {
                Notification::create([
                    'type' => 'report_updated',
                    'user_id' => $report->assigned_mentor_id,
                    'report_id' => $report->id,
                    'title' => 'Report Status Updated',
                    'message' => "Report {$report->reference_number} has been updated from '{$oldStatus}' to '" . ucfirst($request->status) . "'",
                    'data' => [
                        'report_id' => $report->id,
                        'reference_number' => $report->reference_number,
                        'old_status' => $oldStatus,
                        'new_status' => $request->status,
                        'response' => $request->response
                    ]
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Response sent successfully',
                    'data' => $report
                ]);
            }

            return redirect()->back()->with('success', 'Response sent successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send response: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send response: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to send response: ' . $e->getMessage());
        }
    }

    //update report status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,assigned,resolved,dismissed'
        ]);

        try {
            $report = HarassmentReport::findOrFail($id);
            $oldStatus = $report->status;
            
            $report->update(['status' => $request->status]);

            // Notify assigned mentor if status changed
            if ($report->assigned_mentor_id && $oldStatus !== $request->status) {
                Notification::create([
                    'type' => 'status_updated',
                    'user_id' => $report->assigned_mentor_id,
                    'report_id' => $report->id,
                    'title' => 'Report Status Changed',
                    'message' => "Report {$report->reference_number} status changed from '{$oldStatus}' to '{$request->status}'",
                    'data' => [
                        'report_id' => $report->id,
                        'reference_number' => $report->reference_number,
                        'old_status' => $oldStatus,
                        'new_status' => $request->status
                    ]
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'data' => $report
                ]);
            }

            return redirect()->back()->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update status: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $report = HarassmentReport::findOrFail($id);
            
            // Delete related notifications first
            Notification::where('report_id', $report->id)->delete();
            
            // Delete the report
            $report->delete();
            
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report deleted successfully'
                ]);
            }

            return redirect()->route('admin.reports.index')->with('success', 'Report deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete report: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete report: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete report: ' . $e->getMessage());
        }
    }

    /**
     * Export reports to CSV.
     */
    public function exportReports(Request $request)
    {
        try {
            $query = HarassmentReport::with('assignedMentor', 'user');
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('type')) {
                $query->where('incident_type', $request->type);
            }
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            $reports = $query->orderBy('created_at', 'desc')->get();
            
            $filename = "reports_export_" . date('Y-m-d_His') . ".csv";
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add CSV headers
            fputcsv($handle, [
                'Reference Number',
                'Title',
                'Type',
                'Status',
                'Incident Date',
                'Location',
                'Is Anonymous',
                'Submitter Name',
                'Submitter Email',
                'Submitter Phone',
                'Perpetrator Info',
                'Created At',
                'Assigned Mentor',
                'Admin Response',
                'Responded At'
            ]);
            
            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->reference_number,
                    $report->incident_title,
                    ucfirst($report->incident_type),
                    ucfirst($report->status),
                    $report->incident_date,
                    $report->incident_location,
                    $report->is_anonymous ? 'Yes' : 'No',
                    $report->is_anonymous ? 'Anonymous' : ($report->victim_name ?? $report->user?->name ?? 'N/A'),
                    $report->is_anonymous ? 'Anonymous' : ($report->victim_email ?? $report->user?->email ?? 'N/A'),
                    $report->is_anonymous ? 'Anonymous' : ($report->victim_phone ?? 'N/A'),
                    $report->perpetrator_info ?? 'N/A',
                    $report->created_at,
                    $report->assignedMentor?->name ?? 'Not Assigned',
                    $report->admin_response ?? 'N/A',
                    $report->responded_at ?? 'N/A'
                ]);
            }
            
            fclose($handle);
            exit;
        } catch (\Exception $e) {
            Log::error('Failed to export reports: ' . $e->getMessage());
            return back()->with('error', 'Failed to export reports: ' . $e->getMessage());
        }
    }

    /**
     * Get available mentors for assignment.
     */
    public function getAvailableMentors()
    {
        try {
            $mentors = User::where('role', 'mentor')
                ->where('is_active', true)
                ->withCount(['assignedReports' => function($query) {
                    $query->whereIn('status', ['assigned', 'reviewing']);
                }])
                ->orderBy('name')
                ->get()
                ->map(function($mentor) {
                    return [
                        'id' => $mentor->id,
                        'name' => $mentor->name,
                        'email' => $mentor->email,
                        'specialization' => $mentor->specialization ?? 'General',
                        'active_cases' => $mentor->assigned_reports_count,
                        'avatar' => $mentor->photo_url,
                        'phone' => $mentor->phone ?? 'N/A'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $mentors,
                'total' => $mentors->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available mentors: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load mentors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reports statistics for dashboard.
     */
    public function getStats()
    {
        try {
            $stats = [
                'total' => HarassmentReport::count(),
                'pending' => HarassmentReport::where('status', 'pending')->count(),
                'reviewing' => HarassmentReport::where('status', 'reviewing')->count(),
                'assigned' => HarassmentReport::where('status', 'assigned')->count(),
                'resolved' => HarassmentReport::where('status', 'resolved')->count(),
                'dismissed' => HarassmentReport::where('status', 'dismissed')->count(),
                'anonymous' => HarassmentReport::where('is_anonymous', true)->count(),
                'this_week' => HarassmentReport::where('created_at', '>=', now()->subDays(7))->count(),
                'this_month' => HarassmentReport::where('created_at', '>=', now()->subMonth())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Return the authenticated user's own submitted reports.
     * Used by the mobile app "My Reports" screen.
     */
    public function myReports(Request $request)
    {
        try {
            $user = Auth::user();

            $reports = HarassmentReport::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    $q->where('is_anonymous', false)
                      ->where('victim_email', $user->email);
                })
                ->with(['assignedMentor:id,name'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $data = $reports->map(function ($r) {
                return [
                    'id'               => $r->id,
                    'reference_number' => $r->reference_number,
                    'incident_type'    => $r->incident_type,
                    'incident_title'   => $r->incident_title,
                    'status'           => $r->status,
                    'is_anonymous'     => $r->is_anonymous,
                    'submitted_at'     => $r->created_at->format('M d, Y'),
                    'has_response'     => !empty($r->admin_response),
                    'response'         => $r->admin_response,
                    'responded_at'     => $r->responded_at
                        ? \Carbon\Carbon::parse($r->responded_at)->format('M d, Y')
                        : null,
                    'assigned_mentor'  => $r->assignedMentor
                        ? ['id' => $r->assignedMentor->id, 'name' => $r->assignedMentor->name]
                        : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $data,
                'total'   => $reports->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('myReports error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load reports.'], 500);
        }
    }

    /**
     * Public reference-code lookup — used by anonymous users to track their report.
     * Returns only safe fields (no victim PII, no perpetrator details).
     */
    public function showByReference($referenceNumber)
    {
        try {
            $report = HarassmentReport::where('reference_number', $referenceNumber)->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report found with that reference number.',
                ], 404);
            }

            $mentor = null;
            if ($report->assigned_mentor_id) {
                $m = User::find($report->assigned_mentor_id);
                if ($m) $mentor = ['name' => $m->name];
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'reference_number' => $report->reference_number,
                    'incident_type'    => $report->incident_type,
                    'status'           => $report->status,
                    'submitted_at'     => $report->created_at->format('M d, Y'),
                    'has_response'     => !empty($report->admin_response),
                    'response'         => $report->admin_response,
                    'responded_at'     => $report->responded_at
                        ? \Carbon\Carbon::parse($report->responded_at)->format('M d, Y')
                        : null,
                    'assigned_mentor'  => $mentor,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('showByReference error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lookup failed.'], 500);
        }
    }

    /**
     * Send assignment notification to victim.
     */
    private function sendAssignmentNotificationToVictim($report, $mentor)
    {
        try {
            // You can implement email notification here
            Log::info("Report {$report->reference_number} assigned to mentor {$mentor->name}", [
                'report_id' => $report->id,
                'mentor_id' => $mentor->id,
                'victim_email' => $report->victim_email
            ]);
            
            // Example email implementation (uncomment when email is configured):
            /*
            Mail::send('emails.report-assigned', [
                'report' => $report,
                'mentor' => $mentor
            ], function ($message) use ($report) {
                $message->to($report->victim_email)
                    ->subject('Your Report Has Been Assigned to a Mentor');
            });
            */
        } catch (\Exception $e) {
            Log::error('Failed to send assignment notification to victim: ' . $e->getMessage());
        }
    }

    /**
     * Send response to victim.
     */
    private function sendResponseToVictim($report)
    {
        try {
            Log::info("Response sent to victim for report {$report->reference_number}", [
                'report_id' => $report->id,
                'victim_email' => $report->victim_email,
                'status' => $report->status
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send response to victim: ' . $e->getMessage());
        }
    }
}