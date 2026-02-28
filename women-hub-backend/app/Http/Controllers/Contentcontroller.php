<?php

namespace App\Http\Controllers;

use App\Models\HygieneArticle;
use App\Models\GeneralGuide;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function hygieneArticles(Request $request)
    {
        $category = $request->query('category');

        $query = HygieneArticle::where('is_published', true);
        if ($category) {
            $query->where('category', $category);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function hygieneArticle(HygieneArticle $article)
    {
        return response()->json($article);
    }

    public function generalGuides(Request $request)
    {
        $category = $request->query('category');

        $query = GeneralGuide::where('is_published', true);
        if ($category) {
            $query->where('category', $category);
        }

        return response()->json($query->get());
    }

    public function emergencyContacts()
    {
        $contacts = EmergencyContact::where('is_active', true)
            ->orderBy('type')
            ->get();

        return response()->json($contacts);
    }
}