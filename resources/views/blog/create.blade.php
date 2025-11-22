@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="glass p-5">
                <h2 class="text-white mb-4">
                    <i class="bi bi-journal-text"></i> Create Blog Post
                </h2>

                <form method="POST" action="{{ route('blog.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-white">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Content</label>
                        <textarea class="form-control" name="content" rows="10" required>{{ old('content') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Featured Image (Optional)</label>
                        <input type="file" class="form-control" name="featured_image" accept="image/*">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-glass">Publish Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

