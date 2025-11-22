@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="glass p-5 mb-4">
                <h1 class="text-white mb-3">{{ $post->title }}</h1>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span class="text-white-50">
                            <i class="bi bi-person"></i> {{ $post->user->name }}
                        </span>
                        <span class="text-white-50 ms-3">
                            <i class="bi bi-calendar"></i> {{ $post->created_at->format('M d, Y') }}
                        </span>
                        <span class="text-white-50 ms-3">
                            <i class="bi bi-eye"></i> {{ $post->views }} views
                        </span>
                    </div>
                </div>

                @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid rounded mb-4" alt="{{ $post->title }}">
                @endif

                <div class="text-white" style="white-space: pre-wrap;">{{ $post->content }}</div>
            </div>

            <div class="glass p-4">
                <h4 class="text-white mb-3">Comments</h4>
                @forelse($post->comments as $comment)
                <div class="mb-3 pb-3 border-bottom border-white-50">
                    <strong class="text-white">{{ $comment->user->name }}</strong>
                    <span class="text-white-50">{{ $comment->created_at->diffForHumans() }}</span>
                    <p class="text-white mt-2">{{ $comment->content }}</p>
                </div>
                @empty
                <p class="text-white-50">No comments yet.</p>
                @endforelse

                @auth
                <form method="POST" action="{{ route('comments.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="commentable_type" value="App\Models\BlogPost">
                    <input type="hidden" name="commentable_id" value="{{ $post->id }}">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="Add a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-glass">Post Comment</button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

