<?php
// Landing page - entry point for the application
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartResume - AI-Powered Resume Builder & Analyzer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo i {
            color: #8b5cf6;
            position: relative;
        }

        .logo i::after {
            content: '\f005';
            font-family: 'Font Awesome 6 Free';
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
            color: #fbbf24;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: white;
        }

        .nav-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
        }

        .nav-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(139, 92, 246, 0.2);
            color: #c4b5fd;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            border: 1px solid rgba(139, 92, 246, 0.3);
            margin-bottom: 2rem;
            font-size: 0.9rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-title {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #cbd5e1;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(139, 92, 246, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #cbd5e1;
            border: 2px solid #475569;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #8b5cf6;
            color: white;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out 0.8s both;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            background: #020617;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #8b5cf6;
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.1);
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        .feature-icon.purple {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }

        .feature-icon.blue {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .feature-icon.yellow {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Benefits Section */
        .benefits {
            padding: 6rem 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }

        .benefits-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .benefits-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
        }

        .benefits-content p {
            font-size: 1.2rem;
            color: #cbd5e1;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .benefits-list {
            list-style: none;
        }

        .benefits-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .benefits-list i {
            color: #10b981;
            font-size: 1.2rem;
        }

        .demo-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }

        .score-display {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .score-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .score-header i {
            color: #8b5cf6;
        }

        .score-header span {
            color: white;
            font-weight: 600;
        }

        .score-value {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .score-status {
            color: #10b981;
            font-size: 0.9rem;
        }

        .progress-item {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .progress-label {
            color: white;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .progress-bar {
            width: 100%;
            height: 0.5rem;
            background: #374151;
            border-radius: 0.25rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 0.25rem;
            transition: width 0.3s ease;
        }

        .progress-fill.purple {
            background: #8b5cf6;
            width: 80%;
        }

        .progress-fill.green {
            background: #10b981;
            width: 100%;
        }

        /* CTA Section */
        .cta {
            padding: 6rem 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(236, 72, 153, 0.2));
            text-align: center;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color:rgb(202, 185, 185); ;
            margin-bottom: 1.5rem;
        }

        .cta p {
            font-size: 1.2rem;
            color:rgb(192, 180, 180);
            margin-bottom: 2rem;
        }

        .cta-note {
            color: #94a3b8;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.4);
            padding: 3rem 0;
            text-align: center;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .footer-logo i {
            color: #8b5cf6;
            font-size: 1.5rem;
        }

        .footer-logo span {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .footer-description {
            color:rgb(255, 255, 255);
            margin-bottom: 2rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color:rgb(255, 255, 255);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .benefits-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }
        }


        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="" class="logo">
                <i class="fas fa-file-alt"></i>
                <span>SmartResume</span>
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#benefits">How it Works</a>
                <a href="login.php" class="nav-btn">Sign In</a>
                <a href="signup.php" class="nav-btn primary">Get Started</a>
            </div>
        </div>
    </nav>


    <section class="hero">
        <div class="hero-container">
            <div class="hero-badge">
                <i class="fas fa-bolt"></i>
                AI-Powered Resume Intelligence
            </div>
            
            <h1 class="hero-title">
                Build Resumes That <span class="hero-gradient">Get Noticed</span>
            </h1>
            
            <p class="hero-subtitle">
                Create professional resumes with AI assistance, analyze existing ones for improvements, 
                and detect AI-generated content. Your dream job is just one smart resume away.
            </p>
            
            <div class="hero-buttons">
                <a href="signup.php?force_signup=1" class="btn btn-primary">
                    <i class="fas fa-rocket"></i>
                    Start Building Free
                </a>
             
            </div>
            
 
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="features-container">
            <div class="section-header">
                <h2 class="section-title">Why Choose SmartResume?</h2>
                <p class="section-subtitle">
                    Powered by cohereAI technology to give you the competitive edge in today's job market
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon purple">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3 class="feature-title">AI-Powered Suggestions</h3>
                    <p class="feature-description">
                        Get intelligent recommendations to optimize your resume content, formatting, 
                        and keywords for maximum impact with hiring managers and ATS systems.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    <h3 class="feature-title">Smart Analysis & Detection</h3>
                    <p class="feature-description">
                        Analyze existing resumes for strengths and weaknesses. Our advanced AI can 
                        detect AI-generated content to ensure authenticity and originality.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="feature-title">Professional Templates</h3>
                    <p class="feature-description">
                        Choose from a curated collection of modern, ATS-friendly templates designed 
                        by industry experts and optimized for different career fields.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="benefits">
        <div class="benefits-container">
            <div class="benefits-content">
                <h2>Transform Your Career with Smart Technology</h2>
                <p>
                    SmartResume combines artificial intelligence with proven hiring insights to help you 
                    create resumes that stand out in today's competitive job market.
                </p>
                
                <ul class="benefits-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        ATS-optimized formatting that passes screening systems
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Industry-specific keywords and phrases
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Real-time content analysis and scoring
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Multiple export formats (PDF, Word, TXT)
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        AI content detection and authenticity verification
                    </li>
                </ul>
            </div>
            
            <div class="demo-card">
                <div class="score-display">
                    <div class="score-header">
                        <i class="fas fa-chart-line"></i>
                        <span>Resume Score</span>
                    </div>
                    <div class="score-value">92/100</div>
                    <div class="score-status">Excellent - Ready to submit!</div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">Keywords Optimization</div>
                    <div class="progress-bar">
                        <div class="progress-fill purple"></div>
                    </div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">ATS Compatibility</div>
                    <div class="progress-bar">
                        <div class="progress-fill green"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-container">
            <h2>Ready to Land Your Dream Job?</h2>
            <p>
                Join thousands of professionals who've transformed their careers with SmartResume. 
                Start building your perfect resume today.
            </p>
            <a href="signup.php?force_signup=1" class="btn btn-primary">
                <i class="fas fa-rocket"></i>
                Start Building Your Resume
            </a>
            <p class="cta-note">No credit card required • Free forever plan available</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <i class="fas fa-file-alt"></i>
                <span>SmartResume</span>
            </div>
            <p class="footer-description">
                Empowering careers through intelligent resume technology
            </p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Support</a>
                <a href="#">Contact Us</a>
                <a href="#">About</a>
            </div>
        </div>
    </footer>

    <script>
        // Simple scroll effect for navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(15, 23, 42, 0.98)';
            } else {
                navbar.style.background = 'rgba(15, 23, 42, 0.95)';
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.href && !this.href.includes('#')) {
                    this.style.opacity = '0.8';
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                }
            });
        });
    </script>
</body>
</html>