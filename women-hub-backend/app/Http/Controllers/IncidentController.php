<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Incident; // Import your newly configured model

class IncidentController extends Controller
{
    public function incident(Request $request)
    {
        // 1. Validate the incoming request text first
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            // 2. Fetch predictions from FastAPI server
            $response = Http::asJson()->post('http://127.0.0.1:5000/analyze', [
                'text' => $request->input('content')
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'AI service unavailable'
                ], 500);
            }

            $analysis = $response->json();

            // 🔥 FIX: Intercept the low confidence fallback message from FastAPI
            if (isset($analysis['message'])) {
                return response()->json([
                    'success' => false,
                    'message' => $analysis['message']
                ], 422); // 422 is perfect for semantic validation rejections
            }

            // 3. Persist the valid incident tracking data to the database
            $incident = Incident::create([
                'content'            => $request->input('content'),
                'expertise_assigned' => $analysis['expertise'] ?? 'Unclassified',
                'risk_level'         => $analysis['risk_level'] ?? 'Low',
                'confidence_score'   => $analysis['confidence'] ?? 0.0,
            ]);

            // 4. Return everything back to the UI
            return response()->json([
                'success' => true,
                'incident_id' => $incident->id,
                'analysis' => [
                    'expertise' => $incident->expertise_assigned,
                    'risk_level' => $incident->risk_level,
                    'confidence' => $incident->confidence_score,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'AI service error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}