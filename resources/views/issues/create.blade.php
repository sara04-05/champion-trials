@extends('layouts.app')

@section('title', 'Report an Issue')

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="glass p-5">
                <h2 class="text-white mb-4">
                    <i class="bi bi-plus-circle"></i> Report an Issue
                </h2>

                <form method="POST" action="{{ route('issues.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-white">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Description</label>
                        <textarea class="form-control" name="description" rows="4" required>{{ old('description') }}</textarea>
                        <small class="text-white-50">Our AI will automatically categorize your issue based on your description.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white">State</label>
                            <input type="text" class="form-control" name="state" value="{{ auth()->user()->state ?? old('state') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">City</label>
                            <input type="text" class="form-control" name="city" value="{{ auth()->user()->city ?? old('city') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Address (Optional)</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Location on Map</label>
                        <div id="map" style="height: 400px; border-radius: 10px;"></div>
                        <input type="hidden" name="latitude" id="latitude" required>
                        <input type="hidden" name="longitude" id="longitude" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Category (Optional - Auto-detected if not selected)</label>
                        <select class="form-select" name="category_id">
                            <option value="">Auto-detect</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Images (Optional)</label>
                        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-glass">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([20.5937, 78.9629], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });

    // Try to get user's location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            map.setView([lat, lng], 13);
            
            marker = L.marker([lat, lng]).addTo(map);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });
    }
</script>
@endpush
@endsection

