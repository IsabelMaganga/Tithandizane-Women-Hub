<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Incident;
use App\Models\User;
use App\Models\GeneralGuide;
use App\Models\HygieneArticle;


class IncidentController extends Controller
{
    public function incident(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            // 1. Call your NLP model
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

            $predictedCategory = $analysis['expertise'] ?? 'Unclassified';
            $riskLevel         = $analysis['risk_level'] ?? 'Low';
            $confidence        = $analysis['confidence'] ?? 0.0;

            // 2. Save incident
            $incident = Incident::create([
                'content'            => $request->input('content'),
                'expertise_assigned' => $predictedCategory,
                'risk_level'         => $riskLevel,
                'confidence_score'   => $confidence,
            ]);

            // 3. Fetch real topics from both tables
            $guides = GeneralGuide::where('category', $predictedCategory)
                ->limit(3)
                ->get(['id', 'title', 'category']);

            $articles = HygieneArticle::where('category', $predictedCategory)
                ->limit(3)
                ->get(['id', 'title', 'category']);

            // 4. Merge and format topics — tag each with its source
            $topics = collect();

            $topics = $topics->merge(
                $guides->map(fn($g) => [
                    'id'       => 'guide_' . $g->id,
                    'title'    => $g->title,
                    'category' => $g->category,
                    'type'     => 'general_guide',
                ])
            );

            $topics = $topics->merge(
                $articles->map(fn($a) => [
                    'id'       => 'article_' . $a->id,
                    'title'    => $a->title,
                    'category' => $a->category,
                    'type'     => 'hygiene_article',
                ])
            );

            // 5. Fallback if both tables returned nothing for this category
            if ($topics->isEmpty()) {
                $topics = collect([
                    [
                        'id'       => 'fallback_1',
                        'title'    => "Understanding and Managing {$predictedCategory}",
                        'category' => $predictedCategory,
                        'type'     => 'fallback',
                    ],
                    [
                        'id'       => 'fallback_2',
                        'title'    => "Building coping tools for {$predictedCategory}",
                        'category' => $predictedCategory,
                        'type'     => 'fallback',
                    ],
                ]);
            }

            // 6. Query matching mentors
            $matchedMentors = User::where('role', 'mentor')
                ->where('status', 'active')
                ->whereHas('expertises', function ($query) use ($predictedCategory) {
                    $query->where('name', $predictedCategory);
                })
                ->with(['expertises' => function ($query) {
                    $query->select('expertises.id', 'expertises.name');
                }])
                ->get(['id', 'name', 'bio', 'photo']);

            $transformedMentors = $matchedMentors->map(fn($mentor) => [
                'id'        => (string) $mentor->id,
                'name'      => $mentor->name,
                'expertise' => $mentor->expertises->isNotEmpty()
                    ? $mentor->expertises->pluck('name')->implode(', ')
                    : 'General Mentor',
                'photo'     => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
            ]);

            return response()->json([
                'success'     => true,
                'incident_id' => $incident->id,
                'topics'      => $topics->values(),
                'mentors'     => $transformedMentors,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }
}