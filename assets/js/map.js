// fixIT - Map JavaScript (Leaflet/OpenStreetMap)

let map;
let markers = [];
let currentFilters = {
    category: 'all',
    status: 'all',
    urgency: 'all'
};

// Initialize Map
function initMap() {
    // Default location (Pristina, Kosovo)
    const defaultLocation = [42.6629, 21.1655];
    
    // Initialize Leaflet map
    map = L.map('map').setView(defaultLocation, 12);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Load issues
    loadIssues();
    
    // Setup map click handler for reporting
    if (isLoggedIn()) {
        map.on('click', function(event) {
            if (window.reportMode) {
                openReportModal(event.latlng);
            }
        });
    }
    
    // Setup filters
    setupMapFilters();
}

// Load Issues from API
async function loadIssues() {
    try {
        const params = new URLSearchParams({
            action: 'all',
            ...currentFilters
        });
        
        const response = await fetch(`api/issues.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
            displayIssues(data.issues);
        }
    } catch (error) {
        console.error('Error loading issues:', error);
    }
}

// Display Issues on Map
function displayIssues(issues) {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    issues.forEach(issue => {
        const marker = createIssueMarker(issue);
        markers.push(marker);
    });
}

// Create Issue Marker
function createIssueMarker(issue) {
    const categoryColors = {
        'pothole': '#FF6B6B',
        'broken_light': '#FFD93D',
        'traffic': '#6BCF7F',
        'trash': '#4ECDC4',
        'environmental': '#95E1D3',
        'safety': '#F38181',
        'other': '#AA96DA'
    };
    
    const color = categoryColors[issue.category] || '#AA96DA';
    
    // Create custom icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
    
    const marker = L.marker(
        [parseFloat(issue.latitude), parseFloat(issue.longitude)],
        { icon: customIcon }
    ).addTo(map);
    
    // Info popup content
    const content = `
        <div class="issue-info">
            <h4>${escapeHtml(issue.title)}</h4>
            <p><strong>Category:</strong> ${escapeHtml(issue.category.replace('_', ' '))}</p>
            <p><strong>Status:</strong> <span class="issue-status ${issue.status}">${issue.status.replace('_', ' ')}</span></p>
            <p><strong>Urgency:</strong> ${escapeHtml(issue.urgency_level)}</p>
            <p><strong>Reported by:</strong> ${escapeHtml(issue.username)}</p>
            <p><strong>Upvotes:</strong> ${issue.upvotes || 0}</p>
            <button onclick="viewIssueDetails(${issue.id})" class="btn btn-primary mt-2" style="width: 100%;">View Details</button>
        </div>
    `;
    
    marker.bindPopup(content);
    
    return marker;
}

// Setup Map Filters
function setupMapFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const urgencyFilter = document.getElementById('urgencyFilter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            currentFilters.category = this.value;
            loadIssues();
        });
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentFilters.status = this.value;
            loadIssues();
        });
    }
    
    if (urgencyFilter) {
        urgencyFilter.addEventListener('change', function() {
            currentFilters.urgency = this.value;
            loadIssues();
        });
    }
}

// View Issue Details
function viewIssueDetails(issueId) {
    window.location.href = `issue-details.php?id=${issueId}`;
}

// Open Report Modal
function openReportModal(latlng) {
    window.location.href = `report.php?lat=${latlng.lat}&lng=${latlng.lng}`;
}

// Enable Report Mode
function enableReportMode() {
    window.reportMode = true;
    map.getContainer().style.cursor = 'crosshair';
    showAlert('Click on the map to report an issue', 'success');
}

function disableReportMode() {
    window.reportMode = false;
    map.getContainer().style.cursor = '';
}

// Utility Functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Check if user is logged in (from main.js)
function isLoggedIn() {
    return window.currentUser !== undefined;
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the map container to be ready
    setTimeout(initMap, 100);
});
