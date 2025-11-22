<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        $comment = Comment::create($validated);

        $this->gamificationService->awardPoints(auth()->user(), 'comment_posted', 3);

        return back()->with('success', 'Comment added successfully!');
    }
}

