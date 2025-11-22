<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_issues' => Issue::count(),
            'pending_issues' => Issue::where('status', 'pending')->count(),
            'in_progress_issues' => Issue::where('status', 'in_progress')->count(),
            'fixed_issues' => Issue::where('status', 'fixed')->count(),
            'total_users' => User::count(),
            'total_posts' => BlogPost::count(),
        ];

        $recentIssues = Issue::with(['user', 'category'])
            ->latest()
            ->limit(10)
            ->get();

        $issuesByCategory = Issue::selectRaw('category_id, count(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $issuesByStatus = Issue::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentIssues', 'issuesByCategory', 'issuesByStatus'));
    }

    public function issues()
    {
        $issues = Issue::with(['user', 'category', 'assignedWorker'])
            ->latest()
            ->paginate(20);

        return view('admin.issues', compact('issues'));
    }

    public function updateIssueStatus(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,fixed',
            'assigned_worker_id' => 'nullable|exists:users,id',
        ]);

        $issue->update($validated);

        if ($validated['status'] === 'fixed') {
            $issue->update(['fixed_at' => now()]);
        }

        return back()->with('success', 'Issue status updated!');
    }
}

