@extends('layouts.app')

@section('title', $user->name . ' Profile')

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="glass p-4 mb-4">
                <div class="text-center">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mb-3" width="150" height="150" alt="Avatar">
                    @else
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                        <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    <h3 class="text-white">{{ $user->name }} {{ $user->surname }}</h3>
                    <p class="text-white-50">@{{ $user->username }}</p>
                    <div class="mb-3">
                        <span class="badge bg-success fs-6">{{ $user->points }} Points</span>
                    </div>
                    @if($user->id === auth()->id())
                    <a href="{{ route('profile.edit') }}" class="btn btn-glass">Edit Profile</a>
                    @endif
                </div>
            </div>

            <div class="glass p-4 mb-4">
                <h5 class="text-white mb-3">Badges</h5>
                @forelse($user->badges as $badge)
                <div class="mb-2">
                    <span class="badge bg-primary">{{ $badge->name }}</span>
                </div>
                @empty
                <p class="text-white-50">No badges yet.</p>
                @endforelse
            </div>

            <div class="glass p-4">
                <h5 class="text-white mb-3">Roles</h5>
                @foreach($user->roles as $role)
                <span class="badge bg-info">{{ $role->name }}</span>
                @endforeach
            </div>
        </div>

        <div class="col-md-8">
            <div class="glass p-4 mb-4">
                <h4 class="text-white mb-3">My Reports</h4>
                @forelse($user->issues as $issue)
                <div class="mb-3 pb-3 border-bottom border-white-50">
                    <h5 class="text-white">
                        <a href="{{ route('issues.show', $issue) }}" class="text-white text-decoration-none">
                            {{ $issue->title }}
                        </a>
                    </h5>
                    <span class="badge" style="background-color: {{ $issue->category->color }}">
                        {{ $issue->category->name }}
                    </span>
                    <span class="badge bg-{{ $issue->status === 'fixed' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : 'secondary') }}">
                        {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                    </span>
                    <p class="text-white-50 mt-2">{{ $issue->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-white-50">No reports yet.</p>
                @endforelse
            </div>

            <div class="glass p-4">
                <h4 class="text-white mb-3">My Blog Posts</h4>
                @forelse($user->blogPosts as $post)
                <div class="mb-3 pb-3 border-bottom border-white-50">
                    <h5 class="text-white">
                        <a href="{{ route('blog.show', $post) }}" class="text-white text-decoration-none">
                            {{ $post->title }}
                        </a>
                    </h5>
                    <p class="text-white-50">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-white-50">No blog posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

