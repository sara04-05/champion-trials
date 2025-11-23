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
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .contact-hero {
            background: var(--color-white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 3px solid var(--primary);
        }
        
        .contact-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
        }
        
        .contact-hero h1 i {
            color: var(--primary);
        }
        
        .contact-hero p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .contact-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        
        .contact-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-sm);
        }
        
        .contact-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .section-subtitle {
            text-align: center;
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .contact-item {
            margin-bottom: 20px;
            padding: 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-primary);
            transition: all var(--transition-base);
        }
        
        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
            border-color: var(--primary);
        }
        
        .contact-item i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2.5rem;
            }
            
            .contact-hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero -->
    <div class="contact-hero">
        <h1><i class="fas fa-envelope-open-text"></i> Contact Us</h1>
        <p>We're here to assist you with support, inquiries, or feedback. Let's improve our communities together!</p>
    </div>
    
    <div class="contact-container">
        <div class="contact-card">
            <h1 class="contact-title">Get in Touch</h1>
            <p class="section-subtitle">Reach us through any of the channels below</p>
            
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>Email:</strong> support@fixit.com
                </div>
            </div>
            
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Phone:</strong> +1 (555) 123-4567
                </div>
            </div>
            
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Address:</strong> 123 City Care Street, Urban City, UC 12345
                </div>
            </div>
            
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Business Hours:</strong> Monday - Friday, 9 AM – 5 PM
                </div>
            </div>
            
            <h2 class="section-title" style="margin-top: 50px;">Report a Bug</h2>
            <p class="section-subtitle">Found an issue? Help us improve by describing the problem in detail!</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
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
