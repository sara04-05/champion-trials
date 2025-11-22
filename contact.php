<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contact-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .contact-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
        }
        .contact-title {
            color: var(--text-light);
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .contact-info {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.8;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .contact-item {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .contact-item i {
            color: var(--primary-green);
            margin-right: 10px;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="contact-container">
        <div class="contact-card">
            <h1 class="contact-title">Contact Us</h1>
            <div class="contact-info">
                <p>Have questions, suggestions, or need support? We're here to help!</p>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <strong>Email:</strong> support@citycare.com
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <strong>Phone:</strong> +1 (555) 123-4567
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Address:</strong> 123 City Care Street, Urban City, UC 12345
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <strong>Business Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM
                </div>
                
                <h2 style="color: var(--primary-green); margin-top: 40px; margin-bottom: 20px;">Get in Touch</h2>
                <p>For technical support, feature requests, or general inquiries, please reach out to us through the contact information above. We typically respond within 24-48 hours.</p>
                
                <h2 style="color: var(--primary-green); margin-top: 40px; margin-bottom: 20px;">Report a Bug</h2>
                <p>Found a bug or experiencing technical issues? Please include as much detail as possible about the problem, including your browser, operating system, and steps to reproduce the issue.</p>
            </div>
        </div>
    </div>
    
    <!-- Accessibility Controls -->
    <div class="accessibility-controls">
        <div class="font-size-controls">
            <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
            <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
            <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

