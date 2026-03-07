<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Harassmentreport;
use Illuminate\Http\Request;

class HarassmentReportController extends Controller
{
    public function index()
    {
        $reports = Harassmentreport::latest()->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    public function create()
    {
        return view('admin.reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'report_type' => 'required|string',
            'status' => 'required|in:pending,in_review,resolved'
        ]);

        Harassmentreport::create([
            'description' => $request->description,
            'report_type' => $request->report_type,
            'status' => $request->status,
            'report_id' => 'RPT' . time() . rand(1000, 9999)
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report created successfully.');
    }

    public function show(Harassmentreport $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Harassmentreport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,in_review,resolved'
        ]);

        $report->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Report status updated successfully.');
    }

    public function destroy(Harassmentreport $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
