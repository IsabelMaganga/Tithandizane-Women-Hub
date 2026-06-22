<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HarassmentReport;
use App\Models\User;
use App\Notifications\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HarassmentReportController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Harassment report received', $request->all());

        $validator = Validator::make($request->all(), [
            'incident_type'       => 'required|in:physical,verbal,sexual,cyber,other',
            'incident_title'      => 'required|string|max:255',
            'incident_description'=> 'required|string',
            'incident_location'   => 'required|string',
            'incident_date'       => 'required|date',
            'perpetrator_info'    => 'nullable|string',
            'is_anonymous'        => 'required|boolean',
            'victim_name'         => 'required_if:is_anonymous,false|string|max:255',
            'victim_email'        => 'required_if:is_anonymous,false|email|max:255',
            'victim_phone'        => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $reportData = $request->all();

            if (!$request->is_anonymous && $user) {
                $reportData['victim_name']  = $user->name;
                $reportData['victim_email'] = $user->email;
                $reportData['user_id']      = $user->id;
            }

            $report = HarassmentReport::create($reportData);

            // Notify all admins using Laravel's notification system
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewNotification(
                    'New Harassment Report',
                    "A new harassment report has been submitted. Reference: {$report->reference_number}"
                ));
            }

            DB::commit();

            Log::info('Harassment report saved', ['report_id' => $report->id]);

            return response()->json([
                'success' => true,
                'message' => 'Your harassment report has been submitted successfully',
                'data'    => [
                    'id'               => $report->id,
                    'reference_number' => $report->reference_number,
                    'status'           => $report->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ], 500);
        }
    }

    public function submitAnonymousReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $report = HarassmentReport::create([
                'incident_type'        => 'other',
                'incident_title'       => $request->title,
                'incident_description' => $request->description,
                'incident_location'    => $request->location,
                'incident_date'        => now()->toDateString(),
                'is_anonymous'         => true,
                'status'               => 'pending'
            ]);

            // Notify all admins using Laravel's notification system
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewNotification(
                    'New Anonymous Report',
                    "A new anonymous harassment report has been submitted. Reference: {$report->reference_number}"
                ));
            }

            DB::commit();

            return response()->json([
                'success'          => true,
                'message'          => 'Your harassment report has been submitted successfully',
                'reference_number' => $report->reference_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit anonymous report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ], 500);
        }
    }

    public function userReports()
    {
        $user    = Auth::user();
        $reports = HarassmentReport::where('user_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('is_anonymous', false)
                      ->where('victim_email', $user->email);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $reports
        ]);
    }
}