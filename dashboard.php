<?php
require_once 'auth.php';
requireAuth(); // Redirect to login if not authenticated



// Database connection
$host = 'localhost';
$dbname = 'resume_builder';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist, use mock data
    $pdo = null;
}

// Mock data for demonstration (replace with actual database queries)
if ($pdo) {
    try {
        // Get actual stats from database
        $stmt = $pdo->query("SELECT COUNT(*) as total_resumes FROM resumes WHERE user_id = 1");
        $total_resumes = $stmt->fetch()['total_resumes'] ?? 0;
        
        $stmt = $pdo->query("SELECT COUNT(*) as total_analyses FROM resume_analytics WHERE user_id = 1");
        $total_analyses = $stmt->fetch()['total_analyses'] ?? 0;
        
        $stmt = $pdo->query("SELECT AVG(match_score) as avg_score FROM resume_analytics WHERE user_id = 1");
        $avg_match_score = round($stmt->fetch()['avg_score'] ?? 0);
        
        $stmt = $pdo->query("SELECT COUNT(*) as ai_detections FROM resume_analytics WHERE user_id = 1 AND ai_probability > 50");
        $ai_detections = $stmt->fetch()['ai_detections'] ?? 0;
        
        // Get recent activities
        $stmt = $pdo->query("SELECT * FROM resumes WHERE user_id = 1 ORDER BY created_at DESC LIMIT 4");
        $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        // Fallback to mock data if queries fail
        $pdo = null;
    }
}

// Use mock data if no database connection
if (!$pdo) {
    $total_resumes = 8;
    $total_analyses = 23;
    $avg_match_score = 78;
    $ai_detections = 5;
    
    $recent_activities = [
        ['resume_name' => 'Marketing_Manager_Resume.pdf', 'created_at' => '2024-01-20 14:30:00', 'status' => 'completed'],
        ['resume_name' => 'Software_Engineer_CV.pdf', 'created_at' => '2024-01-20 09:15:00', 'status' => 'analyzed'],
        ['resume_name' => 'Data_Scientist_Resume.pdf', 'created_at' => '2024-01-19 16:45:00', 'status' => 'generated'],
        ['resume_name' => 'Product_Manager_CV.pdf', 'created_at' => '2024-01-18 11:20:00', 'status' => 'uploaded'],
    ];
}

// Get current user info
$current_user = getCurrentUser();
$user_name = $current_user['full_name'] ?? 'User';
$user_email = $current_user['email'] ?? 'user@example.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartResume Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-file-alt"></i>
                    <span>SmartResume</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="templates.php">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Resume</span>
                        </a>
                    </li>
                    <li>
                        <a href="upload-detect.php">
                            <i class="fas fa-search"></i>
                            <span>Analyze Resume</span>
                        </a>
                    </li>
                    <li>
                        <a href="my_resumes.php">
                            <i class="fas fa-folder"></i>
                            <span>My Resumes</span>
                        </a>
                    </li>
                    <li>
                        <a href="templates.php">
                            <i class="fas fa-star"></i>
                            <span>Templates</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
            <span class="user-email"><?php echo htmlspecialchars($user_email); ?></span>
            <a href="logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search resumes..." id="searchInput">
                    </div>
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <div class="welcome-text">
                        <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h2>
                        <p>Here's what's happening with your resumes today.</p>
                    </div>
                    <div class="welcome-actions">
                        <a href="templates.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create New Resume
                        </a>
                    </div>
                </section>

                <!-- Stats Cards -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card" data-count="<?php echo $total_resumes; ?>">
                            <div class="stat-icon blue">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter"><?php echo $total_resumes; ?></h3>
                                <p>Total Resumes</p>
                                <span class="stat-change positive">+2 this month</span>
                            </div>
                        </div>
                        
                        <div class="stat-card" data-count="<?php echo $total_analyses; ?>">
                            <div class="stat-icon green">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter"><?php echo $total_analyses; ?></h3>
                                <p>Analyses Performed</p>
                                <span class="stat-change positive">+5 this week</span>
                            </div>
                        </div>
                        
                        <div class="stat-card" data-count="<?php echo $avg_match_score; ?>">
                            <div class="stat-icon orange">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter"><?php echo $avg_match_score; ?>%</h3>
                                <p>Avg Match Score</p>
                                <span class="stat-change positive">+3% improvement</span>
                            </div>
                        </div>
                        
                        <div class="stat-card" data-count="<?php echo $ai_detections; ?>">
                            <div class="stat-icon purple">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="counter"><?php echo $ai_detections; ?></h3>
                                <p>AI Detections</p>
                                <span class="stat-change neutral">This month</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions & Recent Activity -->
                <section class="content-grid">
                    <div class="quick-actions-card">
                        <h3>Quick Actions</h3>
                        <div class="actions-grid">
                            <a href="templates.php" class="action-item">
                                <div class="action-icon blue">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Create Resume</h4>
                                    <p>Build from scratch</p>
                                </div>
                            </a>
                            
                            <a href="upload-detect.php" class="action-item">
                                <div class="action-icon green">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Analyze Resume</h4>
                                    <p>AI-powered analysis</p>
                                </div>
                            </a>
                            
                            <a href="templates.php" class="action-item">
                                <div class="action-icon orange">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Browse Templates</h4>
                                    <p>Professional designs</p>
                                </div>
                            </a>
                            
                            <a href="job_matcher.php" class="action-item">
                                <div class="action-icon purple">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="action-content">
                                    <h4>Job Matcher</h4>
                                    <p>Find perfect matches</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="recent-activity-card">
                        <h3>Recent Activity</h3>
                        <div class="activity-list">
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-<?php echo $activity['status'] === 'completed' ? 'check' : ($activity['status'] === 'analyzed' ? 'chart-bar' : ($activity['status'] === 'generated' ? 'magic' : 'upload')); ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <p><strong><?php echo ucfirst($activity['status']); ?></strong></p>
                                    <p class="activity-file"><?php echo htmlspecialchars($activity['resume_name']); ?></p>
                                    <span class="activity-time"><?php echo date('M j, g:i A', strtotime($activity['created_at'])); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="activity.php" class="view-all-link">View All Activity</a>
                    </div>
                </section>

                <!-- Performance Overview -->
                <section class="performance-section">
                    <div class="performance-card">
                        <h3>Performance Overview</h3>
                        <div class="performance-grid">
                            <div class="performance-item">
                                <h4>This Week</h4>
                                <div class="performance-stats">
                                    <div class="performance-stat">
                                        <span class="stat-number">3</span>
                                        <span class="stat-label">Resumes</span>
                                    </div>
                                    <div class="performance-stat">
                                        <span class="stat-number">7</span>
                                        <span class="stat-label">Analyses</span>
                                    </div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <h4>This Month</h4>
                                <div class="performance-stats">
                                    <div class="performance-stat">
                                        <span class="stat-number"><?php echo $total_resumes; ?></span>
                                        <span class="stat-label">Resumes</span>
                                    </div>
                                    <div class="performance-stat">
                                        <span class="stat-number"><?php echo $total_analyses; ?></span>
                                        <span class="stat-label">Analyses</span>
                                    </div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <h4>All Time</h4>
                                <div class="performance-stats">
                                    <div class="performance-stat">
                                        <span class="stat-number">45</span>
                                        <span class="stat-label">Resumes</span>
                                    </div>
                                    <div class="performance-stat">
                                        <span class="stat-number">156</span>
                                        <span class="stat-label">Analyses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
