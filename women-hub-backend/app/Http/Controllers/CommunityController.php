<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Get all community posts
     */
    public function index()
{
    $posts = CommunityPost::with([
        'user',
        'comments.user',
    ])
    ->withCount(['likes', 'comments'])
    ->latest()
    ->get();

    return response()->json([
        'success' => true,
        'posts' => $posts,
    ]);
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'text' => 'required|string|max:5000',
        'category' => 'required|string|max:100',
    ]);

    $post = CommunityPost::create([
        'user_id' => auth()->id(),
        'category' => $validated['category'],
        'text' => $validated['text'],
    ]);

    $post->load('user');

    return response()->json([
        'success' => true,
        'message' => 'Post created successfully.',
        'post' => $post,
    ], 201);
}

public function comment(Request $request, CommunityPost $post)
{
    $validated = $request->validate([
        'comment' => 'required|string|max:2000',
    ]);

    $comment = $post->comments()->create([
        'user_id' => auth()->id(),
        'comment' => $validated['comment'],
    ]);

    $comment->load('user');

    return response()->json([
        'success' => true,
        'message' => 'Comment added successfully.',
        'comment' => $comment,
    ], 201);
}

public function like(CommunityPost $post)
{
    $like = CommunityLike::where('community_post_id', $post->id)
        ->where('user_id', auth()->id())
        ->first();

    if ($like) {
        // Unlike (toggle behavior)
        $like->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post unliked',
            'liked' => false,
        ]);
    }

    CommunityLike::create([
        'community_post_id' => $post->id,
        'user_id' => auth()->id(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Post liked',
        'liked' => true,
    ]);
}
}