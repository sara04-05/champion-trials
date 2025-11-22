@extends('layouts.app')

@section('title', 'My Reports')

@section('content')
<div class="container py-5 mt-5">
    <h1 class="text-white mb-4">My Reports</h1>

    <div class="row">
        @forelse($issues as $issue)
        <div class="col-md-6 mb-4">
            <div class="glass p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="text-white">
                            <a href="{{ route('issues.show', $issue) }}" class="text-white text-decoration-none">
                                {{ $issue->title }}
                            </a>
                        </h4>
                        <span class="badge" style="background-color: {{ $issue->category->color }}">
                            {{ $issue->category->name }}
                        </span>
                        <span class="badge bg-{{ $issue->status === 'fixed' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                        </span>
                    </div>
                </div>
                <p class="text-white-50">{{ \Illuminate\Support\Str::limit($issue->description, 150) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-white-50">{{ $issue->created_at->diffForHumans() }}</small>
                    <span class="badge bg-info">{{ $issue->upvotes }} upvotes</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass p-5 text-center">
                <p class="text-white">No reports yet. <a href="{{ route('issues.create') }}" class="text-white">Report your first issue!</a></p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $issues->links() }}
    </div>
</div>
@endsection

