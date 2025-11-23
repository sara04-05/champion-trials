<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            margin: 0;
            color: var(--text-primary);
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

  

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="glassmorphism-modal" style="position: relative; margin: 20px auto;">
                    <h2 class="modal-title">Report an Issue</h2>
                    <form id="reportForm" onsubmit="handleReportIssue(event)">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" id="issueTitle" class="form-control" placeholder="Brief description of the issue" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="issueDescription" class="form-control" rows="5" placeholder="Provide detailed information about the issue..." required></textarea>
                            <small class="text-muted">Our system will automatically categorize your issue based on your description.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Location</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="number" id="issueLat" class="form-control" placeholder="Latitude" step="any" value="<?php echo htmlspecialchars($lat ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" id="issueLng" class="form-control" placeholder="Longitude" step="any" value="<?php echo htmlspecialchars($lng ?? ''); ?>" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" onclick="getCurrentLocation()">
                                Use Current Location
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" id="issueState" class="form-control" value="<?php echo htmlspecialchars($_SESSION['state'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="issueCity" class="form-control" value="<?php echo htmlspecialchars($_SESSION['city'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Urgency Level</label>
                            <select id="issueUrgency" class="form-control" required>
                                <option value="">Select Urgency</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <small class="text-muted">Select the urgency level of this issue</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Photo (Optional)</label>
                            <input type="file" id="issuePhoto" class="form-control" accept="image/*">
                            <small class="text-muted">Upload a photo of the issue</small>
                        </div>
                        
                        <div id="duplicateWarning" class="alert alert-warning" style="display: none;"></div>
                        
                        <div id="categoryPreview" class="alert alert-info" style="display: none;">
                            <strong>Detected Category:</strong> <span id="detectedCategory"></span>
                            <br><strong>Estimated Fix Time:</strong> <span id="estimatedFixTime"></span> days
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Submit Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.getElementById('issueDescription').addEventListener('input', checkDuplicates);

        async function checkDuplicates() {
            const lat = document.getElementById('issueLat').value;
            const lng = document.getElementById('issueLng').value;
            if (lat && lng) {
                try {
                    const response = await fetch(`api/issues.php?action=duplicates&lat=${lat}&lng=${lng}`);
                    const data = await response.json();
                    const warning = document.getElementById('duplicateWarning');
                    if (data.success && data.duplicates.length > 0) {
                        warning.style.display = 'block';
                        warning.innerHTML = `<strong>Warning:</strong> ${data.duplicates.length} similar issue(s) found nearby.`;
                    } else {
                        warning.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error checking duplicates:', error);
                }
            }
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('issueLat').value = position.coords.latitude;
                    document.getElementById('issueLng').value = position.coords.longitude;
                    checkDuplicates();
                });
            } else {
                showAlert('Geolocation is not supported', 'error');
            }
        }

        async function handleReportIssue(event) {
            event.preventDefault();
            const formData = {
                title: document.getElementById('issueTitle').value,
                description: document.getElementById('issueDescription').value,
                latitude: parseFloat(document.getElementById('issueLat').value),
                longitude: parseFloat(document.getElementById('issueLng').value),
                state: document.getElementById('issueState').value,
                city: document.getElementById('issueCity').value,
                urgency: document.getElementById('issueUrgency').value
            };

            try {
                const response = await fetch('api/issues.php?action=report', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();

                if (data.success) {
                    showAlert('Issue reported successfully! You earned 10 points.', 'success');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    showAlert(data.message || 'Failed to report issue', 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            }
        }
    </script>

    <!-- Accessibility Controls -->
    <div class="accessibility-controls">
        <div class="font-size-controls">
            <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
            <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
            <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
        </div>
    </div>
</body>
</html>