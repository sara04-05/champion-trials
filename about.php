<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - fixIT</title>
    
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

        /* Your Original Hero - Untouched */
        .about-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .about-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
            margin-bottom: 20px;
        }

        .about-hero h1 i { color: var(--green); margin-right: 12px; }

        .about-hero p {
            font-size: 1.25rem;
            color: var(--text-light);
            max-width: 720px;
            margin: 0 auto;
        }

        .about-container {
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
            margin: 0 auto 70px;
        }

        .modern-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 50px 40px;
            margin-bottom: 60px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .modern-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        /* Feature Grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 32px;
            margin: 80px 0;
        }

        .feature-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: var(--radius);
            text-align: center;
            border: 1.5px solid transparent;
            transition: var(--transition);
        }

        .feature-card:hover {
            border-color: var(--green);
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.15);
        }

        .feature-icon {
            width: 82px; height: 82px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--green), #0d8f63);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .feature-card:nth-child(3n+1) .feature-icon { background: linear-gradient(135deg, var(--red), #c53030); }
        .feature-card:nth-child(3n+2) .feature-icon { background: linear-gradient(135deg, var(--blue), #2563eb); }

        /* Process Steps */
        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .process-step {
            background: var(--white);
            padding: 40px 30px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
        }

        .step-number {
            width: 60px; height: 60px;
            background: var(--green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 auto 20px;
        }

        /* Values */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 32px;
            margin-top: 50px;
        }

        .value-item {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .value-item:hover { transform: translateY(-8px); }

        .value-item h3 i { color: var(--green); margin-right: 10px; }

        /* Stats */
        .stats-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: var(--radius);
            padding: 100px 50px;
            text-align: center;
            margin: 100px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 50px;
            margin-top: 60px;
        }

        .stat-number {
            font-size: 4.2rem;
            font-weight: 800;
            font-family: 'Manrope', sans-serif;
        }

        .stat-number.green { color: var(--green); }
        .stat-number.red { color: var(--red); }
        .stat-number.blue { color: var(--blue); }

        .stat-label {
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* CTA */
        .cta-section {
            background: var(--white);
            padding: 100px 40px;
            text-align: center;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
        }

        .cta-button {
            display: inline-block;
            padding: 18px 50px;
            background: var(--green);
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(16, 185, 129, 0.4);
        }

        @media (max-width: 768px) {
            .about-hero h1 { font-size: 2.8rem; }
            .section-title { font-size: 2.4rem; }
            .stat-number { font-size: 3.2rem; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Your Original Hero -->
    <div class="about-hero">
        <h1><i class="fas fa-tools"></i> About fixIT</h1>
        <p>Empowering citizens to build better communities through technology, transparency, and active participation</p>
    </div>

    <div class="about-container">

        <!-- Mission -->
        <div class="modern-card">
            <h2 class="section-title">Our Mission</h2>
            <p class="section-subtitle">Transforming how communities identify, report, and resolve local issues</p>
            <p style="text-align:center; max-width:880px; margin:0 auto; font-size:1.15rem; color:#475569;">
                fixIT is a comprehensive digital platform designed to bridge the gap between citizens and local authorities. We believe that every voice matters and that technology can amplify community engagement to create safer, cleaner, and more livable cities.
            </p>
        </div>

        <!-- Features -->
        <h2 class="section-title">Why Choose fixIT?</h2>
        <p class="section-subtitle">Everything you need to make real change</p>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3>Interactive Mapping</h3>
                <p>Pinpoint issues with precision on our beautiful interactive map</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-brain"></i></div>
                <h3>Smart Categorization</h3>
                <p>AI instantly understands and routes your report correctly</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>Community Driven</h3>
                <p>Upvote, comment, and collaborate with neighbors</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-tie"></i></div>
                <h3>Professional Network</h3>
                <p>Verified experts provide insights and solutions</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-trophy"></i></div>
                <h3>Gamification</h3>
                <p>Earn points and badges for making your city better</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Real-Time Tracking</h3>
                <p>Watch your issue move from reported to resolved</p>
            </div>
        </div>

        <!-- How It Works -->
        <div class="modern-card">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Simple steps to make a big impact</p>
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Report an Issue</h3>
                    <p>Use our interactive map to pinpoint and report local issues</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Smart Processing</h3>
                    <p>Our system automatically categorizes and prioritizes your report</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Community Engagement</h3>
                    <p>Others can upvote, comment, and add insights</p>
                </div>
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h3>Track Progress</h3>
                    <p>Monitor status updates in real-time until resolution</p>
                </div>
            </div>
        </div>

        <!-- Our Values -->
        <div class="modern-card">
            <h2 class="section-title">Our Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <h3><i class="fas fa-eye"></i> Transparency</h3>
                    <p>Every report is public, trackable, and accountable from day one.</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-handshake"></i> Community First</h3>
                    <p>We build with citizens, for citizens. Your voice shapes the platform.</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-rocket"></i> Innovation</h3>
                    <p>Using the latest technology to make civic action simple and effective.</p>
                </div>
            </div>
        </div>

        <!-- Our Impact -->
        <div class="stats-section">
            <h2 class="section-title" style="color:white;">Our Impact</h2>
            <p class="section-subtitle">Real numbers from real communities</p>
            <div class="stats-grid">
                <div><div class="stat-number green">1000+</div><div class="stat-label">Issues Reported</div></div>
                <div><div class="stat-number green">500+</div><div class="stat-label">Issues Resolved</div></div>
                <div><div class="stat-number blue">200+</div><div class="stat-label">Active Users</div></div>
                <div><div class="stat-number red">50+</div><div class="stat-label">Cities Served</div></div>
            </div>
        </div>

        <!-- CTA -->
        <div class="cta-section">
            <h2 style="font-size:3rem; margin-bottom:20px;">Ready to Make a Difference?</h2>
            <p style="font-size:1.3rem; color:#475569; max-width:700px; margin:0 auto 40px;">
                Join thousands of citizens already improving their cities with fixIT.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="#" class="cta-button" onclick="openSignupModal(); return false;">Get Started Today</a>
            <?php else: ?>
                <a href="report.php" class="cta-button">Report Your First Issue</a>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>