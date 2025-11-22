@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-5 mt-5">
    <h1 class="text-white mb-4">Admin Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="glass p-4 text-center">
                <h3 class="text-white">{{ $stats['total_issues'] }}</h3>
                <p class="text-white-50">Total Issues</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass p-4 text-center">
                <h3 class="text-white">{{ $stats['pending_issues'] }}</h3>
                <p class="text-white-50">Pending</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass p-4 text-center">
                <h3 class="text-white">{{ $stats['in_progress_issues'] }}</h3>
                <p class="text-white-50">In Progress</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass p-4 text-center">
                <h3 class="text-white">{{ $stats['fixed_issues'] }}</h3>
                <p class="text-white-50">Fixed</p>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="glass p-4">
                <h4 class="text-white mb-3">Issues by Category</h4>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass p-4">
                <h4 class="text-white mb-3">Issues by Status</h4>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="glass p-4">
        <h4 class="text-white mb-3">Recent Issues</h4>
        <div class="table-responsive">
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentIssues as $issue)
                    <tr>
                        <td>{{ $issue->title }}</td>
                        <td>{{ $issue->category->name }}</td>
                        <td>
                            <span class="badge bg-{{ $issue->status === 'fixed' ? 'success' : ($issue->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                            </span>
                        </td>
                        <td>{{ $issue->user->name }}</td>
                        <td>
                            <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-glass">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($issuesByCategory->pluck('category.name')) !!},
            datasets: [{
                label: 'Issues',
                data: {!! json_encode($issuesByCategory->pluck('count')) !!},
                backgroundColor: ['#28a745', '#007bff', '#dc3545', '#ffc107', '#17a2b8']
            }]
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($issuesByStatus->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($issuesByStatus->pluck('count')) !!},
                backgroundColor: ['#6c757d', '#007bff', '#28a745']
            }]
        }
    });
</script>
@endpush
@endsection

