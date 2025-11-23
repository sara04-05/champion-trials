<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #000000 100%);
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 255, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 0, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 0, 0, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .about-hero {
            background: linear-gradient(135deg, rgba(0, 255, 0, 0.15) 0%, rgba(0, 0, 255, 0.15) 50%, rgba(255, 0, 0, 0.15) 100%);
            padding: 120px 20px 100px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 3px solid #00ff00;
            position: relative;
            z-index: 1;
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.03) 10px, rgba(255, 255, 255, 0.03) 20px);
            pointer-events: none;
        }
        
        .about-hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 25px;
            text-shadow: 
                0 0 20px rgba(0, 255, 0, 0.5),
                0 0 40px rgba(0, 0, 255, 0.3),
                3px 3px 0px #000000;
            position: relative;
            z-index: 2;
        }
        
        .about-hero h1 i {
            color: #00ff00;
            text-shadow: 0 0 15px rgba(0, 255, 0, 0.8);
        }
        
        .about-hero p {
            font-size: 1.4rem;
            color: #ffffff;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.9;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            position: relative;
            z-index: 2;
        }
        
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
            position: relative;
            z-index: 1;
        }
        
        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 
                2px 2px 0px #000000,
                0 0 15px rgba(0, 255, 0, 0.4);
        }
        
        .section-subtitle {
            text-align: center;
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        }
        
        .about-card {
            background: rgba(0, 0, 0, 0.7);
            border: 3px solid #ffffff;
            border-radius: 25px;
            padding: 50px;
            margin-bottom: 40px;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.5),
                0 0 20px rgba(0, 255, 0, 0.2),
                inset 0 0 20px rgba(255, 255, 255, 0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .about-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle, rgba(0, 255, 0, 0.1) 0%, transparent 70%),
                radial-gradient(circle, rgba(0, 0, 255, 0.1) 0%, transparent 70%),
                radial-gradient(circle, rgba(255, 0, 0, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .about-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: #00ff00;
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.6),
                0 0 30px rgba(0, 255, 0, 0.4),
                inset 0 0 30px rgba(255, 255, 255, 0.1);
        }
        
        .about-card > * {
            position: relative;
            z-index: 2;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature-card {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(26, 26, 46, 0.8) 100%);
            border: 2px solid #ffffff;
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:nth-child(1) { border-color: #00ff00; }
        .feature-card:nth-child(2) { border-color: #0000ff; }
        .feature-card:nth-child(3) { border-color: #ff0000; }
        .feature-card:nth-child(4) { border-color: #00ff00; }
        .feature-card:nth-child(5) { border-color: #0000ff; }
        .feature-card:nth-child(6) { border-color: #ff0000; }
        
        .feature-card:hover {
            transform: translateY(-15px) rotate(2deg);
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.6),
                0 0 25px currentColor;
        }
        
        .feature-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #ffffff;
            border: 4px solid #ffffff;
            box-shadow: 
                0 0 20px currentColor,
                inset 0 0 20px rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
        }
        
        .feature-card:nth-child(1) .feature-icon { 
            background: #00ff00; 
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .feature-card:nth-child(2) .feature-icon { 
            background: #0000ff; 
            box-shadow: 0 0 30px rgba(0, 0, 255, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .feature-card:nth-child(3) .feature-icon { 
            background: #ff0000; 
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .feature-card:nth-child(4) .feature-icon { 
            background: #00ff00; 
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .feature-card:nth-child(5) .feature-icon { 
            background: #0000ff; 
            box-shadow: 0 0 30px rgba(0, 0, 255, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .feature-card:nth-child(6) .feature-icon { 
            background: #ff0000; 
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(360deg);
        }
        
        .feature-card h3 {
            color: #ffffff;
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
        
        .feature-card p {
            color: #ffffff;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        
        .stats-section {
            background: linear-gradient(135deg, rgba(0, 255, 0, 0.1) 0%, rgba(0, 0, 255, 0.1) 50%, rgba(255, 0, 0, 0.1) 100%);
            border: 3px solid #ffffff;
            border-radius: 25px;
            padding: 60px;
            margin: 50px 0;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.5),
                0 0 25px rgba(255, 255, 255, 0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
        }
        
        .stat-item:nth-child(1) { border-color: #00ff00; }
        .stat-item:nth-child(2) { border-color: #0000ff; }
        .stat-item:nth-child(3) { border-color: #ff0000; }
        .stat-item:nth-child(4) { border-color: #00ff00; }
        
        .stat-item:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px currentColor;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 10px;
            text-shadow: 
                0 0 20px currentColor,
                3px 3px 0px #000000;
        }
        
        .stat-item:nth-child(1) .stat-number { color: #00ff00; }
        .stat-item:nth-child(2) .stat-number { color: #0000ff; }
        .stat-item:nth-child(3) .stat-number { color: #ff0000; }
        .stat-item:nth-child(4) .stat-number { color: #00ff00; }
        
        .stat-label {
            color: #ffffff;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        
        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .process-step {
            position: relative;
            padding: 35px;
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid #ffffff;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .process-step:nth-child(1) { border-color: #00ff00; }
        .process-step:nth-child(2) { border-color: #0000ff; }
        .process-step:nth-child(3) { border-color: #ff0000; }
        .process-step:nth-child(4) { border-color: #00ff00; }
        .process-step:nth-child(5) { border-color: #0000ff; }
        
        .process-step:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 0 25px currentColor;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 20px;
            border: 3px solid #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            box-shadow: 0 0 20px currentColor;
        }
        
        .process-step:nth-child(1) .step-number { background: #00ff00; }
        .process-step:nth-child(2) .step-number { background: #0000ff; }
        .process-step:nth-child(3) .step-number { background: #ff0000; }
        .process-step:nth-child(4) .step-number { background: #00ff00; }
        .process-step:nth-child(5) .step-number { background: #0000ff; }
        
        .process-step h3 {
            color: #ffffff;
            font-size: 1.3rem;
            margin-bottom: 12px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
        
        .process-step p {
            color: #ffffff;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        
        .cta-section {
            text-align: center;
            padding: 70px 20px;
            background: linear-gradient(135deg, rgba(0, 255, 0, 0.15) 0%, rgba(0, 0, 255, 0.15) 50%, rgba(255, 0, 0, 0.15) 100%);
            border-radius: 25px;
            margin: 50px 0;
            border: 3px solid #ffffff;
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.5),
                0 0 30px rgba(255, 255, 255, 0.3);
        }
        
        .cta-section h2 {
            font-size: 3rem;
            color: #ffffff;
            margin-bottom: 25px;
            font-weight: 800;
            text-shadow: 
                2px 2px 0px #000000,
                0 0 20px rgba(0, 255, 0, 0.5);
        }
        
        .cta-section p {
            color: #ffffff;
            font-size: 1.3rem;
            margin-bottom: 35px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        }
        
        .cta-button {
            display: inline-block;
            padding: 18px 45px;
            background: linear-gradient(135deg, #00ff00 0%, #0000ff 50%, #ff0000 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 800;
            font-size: 1.2rem;
            transition: all 0.4s ease;
            box-shadow: 
                0 5px 20px rgba(0, 0, 0, 0.5),
                0 0 25px rgba(255, 255, 255, 0.3);
            border: 3px solid #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.6),
                0 0 40px rgba(255, 255, 255, 0.5);
            color: #ffffff;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .value-item {
            padding: 30px;
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid #ffffff;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        .value-item:nth-child(1) { border-color: #00ff00; }
        .value-item:nth-child(2) { border-color: #0000ff; }
        .value-item:nth-child(3) { border-color: #ff0000; }
        
        .value-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 0 25px currentColor;
        }
        
        .value-item h3 {
            color: #ffffff;
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
        
        .value-item h3 i {
            margin-right: 10px;
        }
        
        .value-item:nth-child(1) h3 i { color: #00ff00; }
        .value-item:nth-child(2) h3 i { color: #0000ff; }
        .value-item:nth-child(3) h3 i { color: #ff0000; }
        
        .value-item p {
            color: #ffffff;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        
        .mission-text {
            color: #ffffff;
            line-height: 2;
            font-size: 1.15rem;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        }
        
        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.8rem;
            }
            
            .about-hero p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <div class="about-hero">
        <h1><i class="fas fa-tools"></i> About fixIT</h1>
        <p>Empowering citizens to build better communities through technology, transparency, and active participation ✨</p>
    </div>
    
    <div class="about-container">
        <!-- Mission Section -->
        <div class="about-card">
            <h2 class="section-title">Our Mission</h2>
            <p class="section-subtitle">Transforming how communities identify, report, and resolve local issues</p>
            <div class="mission-text">
                <p>fixIT is a comprehensive digital platform designed to bridge the gap between citizens and local authorities. We believe that every voice matters and that technology can amplify community engagement to create safer, cleaner, and more livable cities. 🌟</p>
                <p style="margin-top: 25px;">Our mission is to empower citizens with the tools they need to report issues, track progress, and actively participate in improving their neighborhoods, while providing local authorities with real-time insights into community needs. 💪</p>
            </div>
        </div>
        
        <!-- Features Grid -->
        <h2 class="section-title">Why Choose fixIT? 🎯</h2>
        <p class="section-subtitle">Powerful features designed to make community engagement simple and effective</p>
        
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Interactive Mapping</h3>
                <p>Pinpoint issues precisely on our interactive map. Visualize all reported problems in your area at a glance! 🗺️</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>Smart Categorization</h3>
                <p>Our AI-powered system automatically categorizes issues and assigns appropriate urgency levels for faster resolution! 🤖</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Community Driven</h3>
                <p>Engage with your neighbors through comments, upvotes, and our community blog. Together we're stronger! 👥</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Professional Network</h3>
                <p>Connect with verified professionals - engineers, inspectors, and experts who provide valuable insights! 🎓</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Gamification</h3>
                <p>Earn points and badges for your contributions. Make civic engagement rewarding and fun! 🏆</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Real-Time Tracking</h3>
                <p>Monitor the status of your reports from submission to resolution. Stay informed every step of the way! 📊</p>
            </div>
        </div>
        
        <!-- How It Works -->
        <div class="about-card">
            <h2 class="section-title">How It Works 🚀</h2>
            <p class="section-subtitle">Simple steps to make a big impact in your community</p>
            
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Report an Issue</h3>
                    <p>Use our interactive map to pinpoint and report local issues like potholes, broken streetlights, traffic problems, or environmental hazards. 📍</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Smart Processing</h3>
                    <p>Our system automatically categorizes your report, assigns urgency levels, and estimates resolution time based on issue type. ⚡</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Community Engagement</h3>
                    <p>Other citizens can upvote important issues, add comments, and share insights. Professionals can provide expert recommendations. 💬</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h3>Track Progress</h3>
                    <p>Monitor your reports from pending to in-progress to fixed. Receive notifications when status updates occur. 📈</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">5</div>
                    <h3>Earn Rewards</h3>
                    <p>Get points for reporting issues, commenting, and contributing. Unlock badges as you help improve your city! 🎁</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Section -->
        <div class="stats-section">
            <h2 class="section-title">Our Impact 📈</h2>
            <p class="section-subtitle">Join thousands of active citizens making a difference</p>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Issues Reported</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Issues Resolved</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Cities Served</div>
                </div>
            </div>
        </div>
        
        <!-- Values Section -->
        <div class="about-card">
            <h2 class="section-title">Our Values 💎</h2>
            <div class="values-grid">
                <div class="value-item">
                    <h3><i class="fas fa-eye"></i> Transparency</h3>
                    <p>We believe in open communication and accountability. Every report is visible, trackable, and transparent. No hidden agendas! 🔍</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-handshake"></i> Community First</h3>
                    <p>Our platform is built by the community, for the community. Your voice matters and drives our development. Together we thrive! 🤝</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-rocket"></i> Innovation</h3>
                    <p>We leverage cutting-edge technology to make civic engagement simple, efficient, and accessible to everyone. Always improving! 🚀</p>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="cta-section">
            <h2>Ready to Make a Difference? 🌟</h2>
            <p>Join the fixIT community today and start improving your city, one report at a time!</p>
            <?php if (!isLoggedIn()): ?>
                <a href="login.php" class="cta-button" onclick="openLoginModal(); return false;">
                    <i class="fas fa-user-plus"></i> Get Started Now
                </a>
            <?php else: ?>
                <a href="report.php" class="cta-button">
                    <i class="fas fa-plus-circle"></i> Report Your First Issue
                </a>
            <?php endif; ?>
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
