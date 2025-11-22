<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function index()
    {
        $posts = BlogPost::with('user')
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('blog', 'public');
        }

        $post = BlogPost::create($validated);

        $this->gamificationService->awardPoints(auth()->user(), 'blog_posted', 15);

        return redirect()->route('blog.show', $post)
            ->with('success', 'Blog post created successfully!');
    }

    public function show(BlogPost $post)
    {
        $post->increment('views');
        $post->load(['user', 'comments.user']);
        return view('blog.show', compact('post'));
    }
}

