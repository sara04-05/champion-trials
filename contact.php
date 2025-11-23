<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - fixIT</title>

    <!-- Same fonts as About page -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include 'includes/theme-loader.php'; ?>

    <style>
        :root {
            --white: #ffffff;
            --offwhite: #fafafa;
            --lightgray: #f1f5f9;
            --midgray: #e2e8f0;
            --text: #0f172a;
            --text-light: #475569;
            --red: #ef4444;
            --blue: #3b82f6;
            --green: #10b981;
            --radius: 24px;
            --shadow: 0 10px 40px rgba(0,0,0,0.07);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--offwhite);
            color: var(--text);
            line-height: 1.8;
        }

        h1, h2, h3, .section-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* EXACT SAME HERO AS ABOUT PAGE */
        .contact-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .contact-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
            margin-bottom: 20px;
        }

        .contact-hero h1 i { color: var(--green); margin-right: 12px; }

        .contact-hero p {
            font-size: 1.25rem;
            color: var(--text-light);
            max-width: 720px;
            margin: 0 auto;
        }

        .contact-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 100px 20px;
        }

        .section-title {
            font-size: 2.8rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title::after {
            content: '';
            width: 90px;
            height: 5px;
            background: linear-gradient(90deg, var(--red), var(--blue));
            border-radius: 3px;
            display: block;
            margin: 20px auto 0;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            font-size: 1.15rem;
            max-width: 680px;
            margin: 0 auto 60px;
        }

        /* Same modern card style */
        .contact-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 60px 50px;
            margin-bottom: 60px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        /* Contact Items – same hover style as feature cards */
        .contact-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px 28px;
            background: var(--white);
            border: 1.5px solid var(--midgray);
            border-radius: var(--radius);
            margin-bottom: 20px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .contact-item:hover {
            transform: translateY(-6px);
            border-color: var(--green);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
        }

        .contact-item i {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--green), #0d8f63);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .contact-item strong {
            color: var(--text);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-hero h1 { font-size: 2.8rem; }
            .section-title { font-size: 2.4rem; }
            .contact-item { flex-direction: column; text-align: center; }
            .contact-item i { width: 70px; height: 70px; font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- SAME HERO AS ABOUT PAGE -->
    <div class="contact-hero">
        <h1><i class="fas fa-envelope-open-text"></i> Contact Us</h1>
        <p>We're here to help with support, feedback, or partnership inquiries. Let's build better cities together!</p>
    </div>

    <div class="contact-container">

        <!-- Contact Info Card -->
        <div class="contact-card">
            <h2 class="section-title">Get in Touch</h2>
            <p class="section-subtitle">Reach out through any of the channels below — we respond fast!</p>

            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>Email us</strong><br>
                    <a href="mailto:support@fixit.com" style="color:var(--green); text-decoration:none;">support@fixit.com</a>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Call us</strong><br>
                    +1 (555) 123-4567
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Visit us</strong><br>
                    123 City Care Street<br>
                    Urban City, UC 12345
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Business Hours</strong><br>
                    Monday – Friday: 9 AM – 5 PM<br>
                    Weekend: Closed
                </div>
            </div>
        </div>

        <!-- Report a Bug / Feedback -->
        <div class="contact-card">
            <h2 class="section-title">Report a Bug or Give Feedback</h2>
            <p class="section-subtitle">Help us make fixIT even better — your input matters!</p>
            <p style="text-align:center; max-width:800px; margin:30px auto; color:#475569; font-size:1.1rem;">
                Found a glitch? Have a feature idea? Just want to say hi?<br>
                Drop us a message at <strong><a href="mailto:feedback@fixit.com" style="color:var(--green);">feedback@fixit.com</a></strong> — we read every single one.
            </p>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>