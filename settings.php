<?php
session_start();

$user_name = $_SESSION['user_name'] ?? 'John Doe';
$user_email = $_SESSION['user_email'] ?? 'john@example.com';

// Handle form submission
if ($_POST) {
    // Process settings update here
    $success_message = "Settings updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .settings-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .settings-section {
            margin-bottom: 2rem;
        }
        
        .settings-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            font-size: 0.9rem;
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .btn-save {
            background: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-save:hover {
            background: #5a67d8;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
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
                    <li>
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
                    <li class="active">
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
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Settings</h1>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="settings-container">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <!-- Profile Settings -->
                        <div class="settings-card">
                            <div class="settings-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user"></i>
                                    Profile Information
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-input" name="full_name" value="<?php echo htmlspecialchars($user_name); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-input" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-input" name="phone" placeholder="+1 (555) 123-4567">
                                </div>
                            </div>
                        </div>

                        <!-- Preferences -->
                        <div class="settings-card">
                            <div class="settings-section">
                                <h3 class="section-title">
                                    <i class="fas fa-sliders-h"></i>
                                    Preferences
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Default Template</label>
                                    <select class="form-select" name="default_template">
                                        <option value="modern">Modern Professional</option>
                                        <option value="creative">Creative Design</option>
                                        <option value="minimal">Minimal Clean</option>
                                        <option value="executive">Executive Style</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Language</label>
                                    <select class="form-select" name="language">
                                        <option value="en">English</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="de">German</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Time Zone</label>
                                    <select class="form-select" name="timezone">
                                        <option value="UTC-8">Pacific Time (UTC-8)</option>
                                        <option value="UTC-5">Eastern Time (UTC-5)</option>
                                        <option value="UTC+0">GMT (UTC+0)</option>
                                        <option value="UTC+1">Central European Time (UTC+1)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="settings-card">
                            <div class="settings-section">
                                <h3 class="section-title">
                                    <i class="fas fa-bell"></i>
                                    Notifications
                                </h3>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="email_notifications" name="email_notifications" checked>
                                    <label for="email_notifications">Email notifications</label>
                                </div>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="resume_tips" name="resume_tips" checked>
                                    <label for="resume_tips">Resume tips and suggestions</label>
                                </div>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="job_alerts" name="job_alerts">
                                    <label for="job_alerts">Job match alerts</label>
                                </div>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="product_updates" name="product_updates" checked>
                                    <label for="product_updates">Product updates and news</label>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="settings-card">
                            <div class="settings-section">
                                <h3 class="section-title">
                                    <i class="fas fa-shield-alt"></i>
                                    Security
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-input" name="current_password" placeholder="Enter current password">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-input" name="new_password" placeholder="Enter new password">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-input" name="confirm_password" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
