@extends('layouts.app')

@section('title', 'Make Your City Better')

@section('content')
<div class="container py-5 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Make Your City Better</h1>
        @auth
        <a href="{{ route('blog.create') }}" class="btn btn-glass">
            <i class="bi bi-plus-circle"></i> New Post
        </a>
        @endauth
    </div>

    <div class="row">
        @forelse($posts as $post)
        <div class="col-md-4 mb-4">
            <div class="glass p-4 h-100">
                @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid rounded mb-3" alt="{{ $post->title }}">
                @endif
                <h4 class="text-white mb-2">
                    <a href="{{ route('blog.show', $post) }}" class="text-white text-decoration-none">
                        {{ $post->title }}
                    </a>
                </h4>
                <p class="text-white-50">{{ \Illuminate\Support\Str::limit($post->content, 150) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-white-50">
                        <i class="bi bi-person"></i> {{ $post->user->name }}
                    </small>
                    <small class="text-white-50">
                        <i class="bi bi-eye"></i> {{ $post->views }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass p-5 text-center">
                <p class="text-white">No blog posts yet. Be the first to share!</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection

