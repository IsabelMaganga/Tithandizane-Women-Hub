<?php

namespace App\Http\Controllers\Admin;  

use App\Http\Controllers\Controller;
use App\Models\HarassmentReport;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HarassmentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = HarassmentReport::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('incident_title', 'like', "%{$request->search}%")
                  ->orWhere('incident_description', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('incident_type', $request->type);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('incident_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('incident_date', '<=', $request->to_date);
        }
        
        $reports = $query->latest()->paginate(10);
        
        // Calculate statistics
        $stats = [
            'total' => HarassmentReport::count(),
            'pending' => HarassmentReport::where('status', 'pending')->count(),
            'reviewing' => HarassmentReport::where('status', 'reviewing')->count(),
            'resolved' => HarassmentReport::where('status', 'resolved')->count(),
            'dismissed' => HarassmentReport::where('status', 'dismissed')->count(),
            'anonymous' => HarassmentReport::where('is_anonymous', true)->count(),
        ];
        
        return view('admin.reports.index', compact('reports', 'stats'));
    }
    
    public function show($id)
    {
        $report = HarassmentReport::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        }
        
        return view('admin.reports.show', compact('report'));
    }
    
    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|min:10',
            'status' => 'required|in:pending,reviewing,resolved,dismissed'
        ]);
        
        $report = HarassmentReport::findOrFail($id);
        $report->admin_response = $request->response;
        $report->responded_by = auth()->id();
        $report->responded_at = now();
        $report->status = $request->status;
        $report->save();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Response submitted successfully');
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,resolved,dismissed'
        ]);
        
        $report = HarassmentReport::findOrFail($id);
        $report->status = $request->status;
        
        if ($request->status === 'resolved') {
            $report->responded_at = now();
        }
        
        $report->save();
        
        return redirect()->back()->with('success', 'Status updated successfully');
    }
    
    public function export(Request $request)
    {
        $query = HarassmentReport::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('incident_type', $request->type);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('incident_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('incident_date', '<=', $request->to_date);
        }
        
        $reports = $query->get();
        
        $filename = "harassment_reports_" . date('Y-m-d_His') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($reports) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            
            fputcsv($file, ['ID', 'Reference', 'Title', 'Type', 'Status', 'Anonymous', 'Victim Name', 'Email', 'Location', 'Date']);
            
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->id,
                    'HR-' . str_pad($report->id, 6, '0', STR_PAD_LEFT),
                    $report->incident_title,
                    $report->incident_type,
                    $report->status,
                    $report->is_anonymous ? 'Yes' : 'No',
                    $report->is_anonymous ? 'Anonymous' : $report->victim_name,
                    $report->is_anonymous ? 'N/A' : $report->victim_email,
                    $report->incident_location,
                    $report->incident_date,
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}