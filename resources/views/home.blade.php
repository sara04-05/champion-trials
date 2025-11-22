@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="map-container">
    <div id="map" style="height: 100vh; width: 100%;"></div>
    
    @guest
    <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 1000;">
        <div class="glass p-5 text-center text-white" style="min-width: 400px;">
            <h2 class="mb-4">
                <i class="bi bi-geo-alt-fill"></i> Select your destination — for a better city.
            </h2>
            <div class="mb-3">
                <select id="stateSelect" class="form-select mb-3">
                    <option value="">Select State</option>
                </select>
                <select id="citySelect" class="form-select mb-3" disabled>
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <a href="{{ route('login') }}" class="btn btn-glass" id="loginBtn" disabled>
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-glass" id="registerBtn" disabled>
                    <i class="bi bi-person-plus"></i> Sign Up
                </a>
            </div>
        </div>
    </div>
    @endguest
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([20.5937, 78.9629], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let markers = [];

    @auth
    fetch('{{ route("api.map.issues") }}')
        .then(response => response.json())
        .then(issues => {
            issues.forEach(issue => {
                const marker = L.marker([issue.latitude, issue.longitude], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="background-color: ${issue.category.color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
                        iconSize: [20, 20]
                    })
                }).addTo(map);
                
                marker.bindPopup(`
                    <strong>${issue.title}</strong><br>
                    Status: ${issue.status}<br>
                    Urgency: ${issue.urgency}<br>
                    Upvotes: ${issue.upvotes}
                `);
                
                markers.push(marker);
            });
        });
    @endauth

    @guest
    const states = ['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'Gujarat'];
    const citiesByState = {
        'Maharashtra': ['Mumbai', 'Pune', 'Nagpur'],
        'Delhi': ['New Delhi', 'Delhi'],
        'Karnataka': ['Bangalore', 'Mysore'],
        'Tamil Nadu': ['Chennai', 'Coimbatore'],
        'Gujarat': ['Ahmedabad', 'Surat']
    };

    const stateSelect = document.getElementById('stateSelect');
    const citySelect = document.getElementById('citySelect');
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');

    states.forEach(state => {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        stateSelect.appendChild(option);
    });

    stateSelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = !this.value;
        
        if (this.value && citiesByState[this.value]) {
            citiesByState[this.value].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
        
        updateButtons();
    });

    citySelect.addEventListener('change', updateButtons);

    function updateButtons() {
        const enabled = stateSelect.value && citySelect.value;
        loginBtn.disabled = !enabled;
        registerBtn.disabled = !enabled;
        
        if (enabled) {
            loginBtn.href = '{{ route("login") }}?state=' + stateSelect.value + '&city=' + citySelect.value;
            registerBtn.href = '{{ route("register") }}?state=' + stateSelect.value + '&city=' + citySelect.value;
        }
    }
    @endguest
</script>
@endpush
@endsection

