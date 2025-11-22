<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueCategory;
use App\Services\IssueCategorizationService;
use App\Services\DuplicateDetectionService;
use App\Services\FixTimePredictionService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    public function __construct(
        private IssueCategorizationService $categorizationService,
        private DuplicateDetectionService $duplicateService,
        private FixTimePredictionService $fixTimeService,
        private GamificationService $gamificationService
    ) {}

    public function index()
    {
        $issues = Issue::with(['user', 'category', 'assignedWorker'])
            ->latest()
            ->paginate(20);

        return view('issues.index', compact('issues'));
    }

    public function create()
    {
        $categories = IssueCategory::all();
        return view('issues.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:issue_categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Auto-categorize if not provided
        if (!$request->category_id) {
            $category = $this->categorizationService->categorize(
                $validated['title'],
                $validated['description']
            );
            $validated['category_id'] = $category->id;
        }

        // Auto-detect urgency
        $validated['urgency'] = $this->categorizationService->detectUrgency(
            $validated['title'],
            $validated['description']
        );

        // Predict fix time
        $category = IssueCategory::find($validated['category_id']);
        $validated['estimated_fix_days'] = $this->fixTimeService->predict(
            $category,
            $validated['urgency']
        );

        $validated['user_id'] = auth()->id();

        $issue = Issue::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('issues', 'public');
                $issue->images()->create([
                    'image_path' => $path,
                    'type' => 'before',
                ]);
            }
        }

        // Check for duplicates
        $nearby = $this->duplicateService->findNearbyIssues(
            $validated['latitude'],
            $validated['longitude']
        );

        // Award points
        $this->gamificationService->awardPoints(auth()->user(), 'issue_reported', 10);

        return redirect()->route('issues.show', $issue)
            ->with('success', 'Issue reported successfully!')
            ->with('nearby_issues', $nearby);
    }

    public function show(Issue $issue)
    {
        $issue->load(['user', 'category', 'assignedWorker', 'images', 'updates', 'comments.user']);
        return view('issues.show', compact('issue'));
    }

    public function upvote(Issue $issue)
    {
        $user = auth()->user();
        
        if ($issue->upvoters()->where('user_id', $user->id)->exists()) {
            $issue->upvoters()->detach($user->id);
            $issue->decrement('upvotes');
        } else {
            $issue->upvoters()->attach($user->id);
            $issue->increment('upvotes');
            $this->gamificationService->awardPoints($user, 'issue_upvoted', 2);
        }

        return response()->json(['upvotes' => $issue->fresh()->upvotes]);
    }
}

