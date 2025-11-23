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
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .about-hero {
            background: var(--color-white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 3px solid var(--primary);
        }
        
        .about-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
        }
        
        .about-hero h1 i {
            color: var(--primary);
        }
        
        .about-hero p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .section-subtitle {
            text-align: center;
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .about-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
        }
        
        .about-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }
        
        .mission-text {
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 1.1rem;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            text-align: center;
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--color-white);
            background: var(--primary);
        }
        
        .feature-card h3 {
            color: var(--text-primary);
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        .stats-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 60px;
            margin: 50px 0;
            box-shadow: var(--shadow-sm);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .stat-item {
            text-align: center;
            padding: 30px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            transition: all var(--transition-base);
        }
        
        .stat-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .process-step {
            padding: 30px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }
        
        .process-step:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-white);
            margin-bottom: 20px;
            background: var(--primary);
        }
        
        .process-step h3 {
            color: var(--text-primary);
            font-size: 1.2rem;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .process-step p {
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        .cta-section {
            text-align: center;
            padding: 60px 20px;
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            margin: 50px 0;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .cta-section p {
            color: var(--text-secondary);
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: var(--primary);
            color: var(--color-white);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: #45a049;
            color: var(--color-white);
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .value-item {
            padding: 30px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }
        
        .value-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .value-item h3 {
            color: var(--text-primary);
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .value-item h3 i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .value-item p {
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }
            
            .about-hero p {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 2rem;
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
        <p>Empowering citizens to build better communities through technology, transparency, and active participation</p>
    </div>
    
    <div class="about-container">
        <!-- Mission Section -->
        <div class="about-card">
            <h2 class="section-title">Our Mission</h2>
            <p class="section-subtitle">Transforming how communities identify, report, and resolve local issues</p>
            <div class="mission-text">
                <p>fixIT is a comprehensive digital platform designed to bridge the gap between citizens and local authorities. We believe that every voice matters and that technology can amplify community engagement to create safer, cleaner, and more livable cities.</p>
                <p style="margin-top: 25px;">Our mission is to empower citizens with the tools they need to report issues, track progress, and actively participate in improving their neighborhoods, while providing local authorities with real-time insights into community needs.</p>
            </div>
        </div>
        
        <!-- Features Grid -->
        <h2 class="section-title">Why Choose fixIT?</h2>
        <p class="section-subtitle">Powerful features designed to make community engagement simple and effective</p>
        
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Interactive Mapping</h3>
                <p>Pinpoint issues precisely on our interactive map. Visualize all reported problems in your area at a glance!</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>Smart Categorization</h3>
                <p>Our AI-powered system automatically categorizes issues and assigns appropriate urgency levels for faster resolution!</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Community Driven</h3>
                <p>Engage with your neighbors through comments, upvotes, and our community blog. Together we're stronger!</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Professional Network</h3>
                <p>Connect with verified professionals - engineers, inspectors, and experts who provide valuable insights!</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Gamification</h3>
                <p>Earn points and badges for your contributions. Make civic engagement rewarding and fun!</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Real-Time Tracking</h3>
                <p>Monitor the status of your reports from submission to resolution. Stay informed every step of the way!</p>
            </div>
        </div>
        
        <!-- How It Works -->
        <div class="about-card">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Simple steps to make a big impact in your community</p>
            
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Report an Issue</h3>
                    <p>Use our interactive map to pinpoint and report local issues like potholes, broken streetlights, traffic problems, or environmental hazards.</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Smart Processing</h3>
                    <p>Our system automatically categorizes your report, assigns urgency levels, and estimates resolution time based on issue type.</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Community Engagement</h3>
                    <p>Other citizens can upvote important issues, add comments, and share insights. Professionals can provide expert recommendations.</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h3>Track Progress</h3>
                    <p>Monitor your reports from pending to in-progress to fixed. Receive notifications when status updates occur.</p>
                </div>
                
                <div class="process-step">
                    <div class="step-number">5</div>
                    <h3>Earn Rewards</h3>
                    <p>Get points for reporting issues, commenting, and contributing. Unlock badges as you help improve your city!</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Section -->
        <div class="stats-section">
            <h2 class="section-title">Our Impact</h2>
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
            <h2 class="section-title">Our Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <h3><i class="fas fa-eye"></i> Transparency</h3>
                    <p>We believe in open communication and accountability. Every report is visible, trackable, and transparent. No hidden agendas!</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-handshake"></i> Community First</h3>
                    <p>Our platform is built by the community, for the community. Your voice matters and drives our development. Together we thrive!</p>
                </div>
                <div class="value-item">
                    <h3><i class="fas fa-rocket"></i> Innovation</h3>
                    <p>We leverage cutting-edge technology to make civic engagement simple, efficient, and accessible to everyone. Always improving!</p>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="cta-section">
            <h2>Ready to Make a Difference?</h2>
            <p>Join the fixIT community today and start improving your city, one report at a time!</p>
            <?php if (!isLoggedIn()): ?>
                <a href="#" class="cta-button" onclick="openSignupModal(); return false;">
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
