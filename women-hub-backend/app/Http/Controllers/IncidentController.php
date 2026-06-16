<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Incident; 
use App\Models\User;

class IncidentController extends Controller
{
    public function incident(Request $request)
    {
        // 1. Validate the incoming query from React Native
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            // 2. Fetch predictions from your FastAPI server
            $response = Http::asJson()->post('http://127.0.0.1:5000/analyze', [
                'text' => $request->input('content')
            ]);

            if (!$response->successful()) {
                return response()->json(['message' => 'AI service unavailable'], 500);
            }

            $analysis = $response->json();

            if (isset($analysis['message'])) {
                return response()->json(['success' => false, 'message' => $analysis['message']], 422);
            }

            // Extract values sent by your NLP model (Fallback to defaults if unclassified)
            $predictedCategory = $analysis['expertise'] ?? 'Unclassified'; 
            $riskLevel = $analysis['risk_level'] ?? 'Low';
            $confidence = $analysis['confidence'] ?? 0.0;

            // 3. Persist the incident log to the database
            $incident = Incident::create([
                'content'            => $request->input('content'),
                'expertise_assigned' => $predictedCategory,
                'risk_level'         => $riskLevel,
                'confidence_score'   => $confidence,
            ]);

            // 4. Query matching mentors (CRITICAL FIX: Explicitly include 'id' so pivot bindings work)
            $matchedMentors = User::where('role', 'mentor')
                ->where('status', 'active')
                ->whereHas('expertises', function ($query) use ($predictedCategory) {
                    $query->where('name', $predictedCategory);
                })
                ->with(['expertises' => function($query) {
                    $query->select('expertises.id', 'expertises.name'); // Scope exact pivot properties
                }])
                ->get(['id', 'name', 'bio', 'photo']); // Safely constraints core user table select fields

            // 5. Format mentors list to perfectly match what your frontend screen expects
            $transformedMentors = $matchedMentors->map(function($mentor) {
                return [
                    'id' => (string) $mentor->id, // Convert to string to match React Native keyExtractor expectations
                    'name' => $mentor->name,
                    'expertise' => $mentor->expertises->isNotEmpty() 
                        ? $mentor->expertises->pluck('name')->implode(', ') 
                        : 'General Mentor', 
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                ];
            });

            // 6. Return response payload with dynamic tracking IDs
            return response()->json([
                'success' => true,
                'incident_id' => $incident->id,
                'topics' => [
                    // Dynamic sub-topics changing based on your category matrix
                    ['id' => 'dynamic_' . $incident->id . '_1', 'title' => "Understanding and Managing {$predictedCategory}", 'category' => $predictedCategory],
                    ['id' => 'dynamic_' . $incident->id . '_2', 'title' => "Building coping tools for {$predictedCategory}", 'category' => $predictedCategory],
                ],
                'mentors' => $transformedMentors
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'AI service error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}