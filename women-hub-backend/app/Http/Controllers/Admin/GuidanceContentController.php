<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuidanceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuidanceContentController extends Controller
{
    public function index(Request $request)
    {
        $query = GuidanceContent::with('mentor');
        
        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }
        
        $guidance = $query->orderByDesc('created_at')->paginate(15);
        
        // Get statistics
        $totalContent = GuidanceContent::count();
        $publishedContent = GuidanceContent::where('status', 'published')->count();
        $unpublishedContent = GuidanceContent::where('status', 'unpublished')->count();
        $generalContent = GuidanceContent::where('category', 'general')->count();
        $hygieneContent = GuidanceContent::where('category', 'menstrual_hygiene')->count();
        
        return view('admin.guidance.index', compact(
            'guidance',
            'totalContent',
            'publishedContent',
            'unpublishedContent',
            'generalContent',
            'hygieneContent'
        ));
    }
    
    public function show($id)
    {
        $content = GuidanceContent::with('mentor')->findOrFail($id);
        return view('admin.guidance.show', compact('content'));
    }
    
    public function destroy($id)
    {
        try {
            $content = GuidanceContent::findOrFail($id);
            
            if ($content->photo) {
                Storage::disk('public')->delete($content->photo);
            }
            
            $content->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Guidance content deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete content: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function publish($id)
    {
        $content = GuidanceContent::findOrFail($id);
        $content->update(['status' => 'published']);
        
        return redirect()->back()->with('success', 'Content published successfully!');
    }
    
    public function unpublish($id)
    {
        $content = GuidanceContent::findOrFail($id);
        $content->update(['status' => 'unpublished']);
        
        return redirect()->back()->with('success', 'Content unpublished successfully!');
    }
}
