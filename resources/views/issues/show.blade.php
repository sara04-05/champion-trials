@extends('layouts.app')

@section('title', $issue->title)

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="glass p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="text-white">{{ $issue->title }}</h2>
                        <span class="badge" style="background-color: {{ $issue->category->color }}">
                            {{ $issue->category->name }}
                        </span>
                        <span class="badge bg-{{ $issue->urgency === 'high' ? 'danger' : ($issue->urgency === 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($issue->urgency) }} Priority
                        </span>
                        <span class="badge bg-{{ $issue->status === 'fixed' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                        </span>
                    </div>
                    <button class="btn btn-glass" onclick="upvoteIssue({{ $issue->id }})">
                        <i class="bi bi-heart-fill"></i> <span id="upvotes">{{ $issue->upvotes }}</span>
                    </button>
                </div>

                <p class="text-white">{{ $issue->description }}</p>

                <div class="mb-3">
                    <strong class="text-white">Location:</strong>
                    <span class="text-white">{{ $issue->address ?? $issue->city }}, {{ $issue->state }}</span>
                </div>

                @if($issue->estimated_fix_days)
                <div class="mb-3">
                    <strong class="text-white">Estimated Fix Time:</strong>
                    <span class="text-white">{{ $issue->estimated_fix_days }} days</span>
                </div>
                @endif

                @if($issue->images->count() > 0)
                <div class="mb-3">
                    <strong class="text-white">Images:</strong>
                    <div class="row mt-2">
                        @foreach($issue->images as $image)
                        <div class="col-md-4 mb-2">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded" alt="Issue image">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="glass p-4 mb-4">
                <h4 class="text-white mb-3">Updates</h4>
                @forelse($issue->updates as $update)
                <div class="mb-3 pb-3 border-bottom border-white-50">
                    <strong class="text-white">{{ $update->user->name }}</strong>
                    <span class="text-white-50">{{ $update->created_at->diffForHumans() }}</span>
                    <p class="text-white mt-2">{{ $update->update_text }}</p>
                </div>
                @empty
                <p class="text-white-50">No updates yet.</p>
                @endforelse
            </div>

            <div class="glass p-4">
                <h4 class="text-white mb-3">Comments</h4>
                @forelse($issue->comments as $comment)
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
                    <input type="hidden" name="commentable_type" value="App\Models\Issue">
                    <input type="hidden" name="commentable_id" value="{{ $issue->id }}">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="Add a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-glass">Post Comment</button>
                </form>
                @endauth
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass p-4 mb-4">
                <h5 class="text-white">Reported by</h5>
                <p class="text-white">{{ $issue->user->name }} {{ $issue->user->surname }}</p>
                <p class="text-white-50">{{ $issue->reported_at->diffForHumans() }}</p>
            </div>

            @if($issue->assignedWorker)
            <div class="glass p-4 mb-4">
                <h5 class="text-white">Assigned Worker</h5>
                <p class="text-white">{{ $issue->assignedWorker->name }}</p>
            </div>
            @endif

            <div class="glass p-4">
                <div id="issueMap" style="height: 300px; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const issueMap = L.map('issueMap').setView([{{ $issue->latitude }}, {{ $issue->longitude }}], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(issueMap);

    L.marker([{{ $issue->latitude }}, {{ $issue->longitude }}]).addTo(issueMap);

    function upvoteIssue(issueId) {
        fetch(`/issues/${issueId}/upvote`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('upvotes').textContent = data.upvotes;
        });
    }
</script>
@endpush
@endsection

