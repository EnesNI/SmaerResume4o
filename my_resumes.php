<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'resume_builder';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user's resumes
    $user_id = $_SESSION['user_id'] ?? 1; // Default to user 1 for demo
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$user_id]);
    $resumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Fallback to mock data if database connection fails
    $resumes = [
        [
            'id' => 1,
            'resume_name' => 'Software Engineer Resume',
            'template_name' => 'Modern Professional',
            'status' => 'completed',
            'created_at' => '2024-01-15 10:30:00',
            'updated_at' => '2024-01-20 14:45:00'
        ],
        [
            'id' => 2,
            'resume_name' => 'Marketing Manager CV',
            'template_name' => 'Creative Design',
            'status' => 'draft',
            'created_at' => '2024-01-10 09:15:00',
            'updated_at' => '2024-01-18 16:20:00'
        ],
        [
            'id' => 3,
            'resume_name' => 'Data Scientist Resume',
            'template_name' => 'Minimal Clean',
            'status' => 'published',
            'created_at' => '2024-01-05 11:00:00',
            'updated_at' => '2024-01-15 13:30:00'
        ]
    ];
}

$user_name = $_SESSION['user_name'] ?? 'John Doe';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resumes - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .resumes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .resume-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .resume-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .resume-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .resume-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .resume-template {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        .resume-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-published {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }
        
        .resume-dates {
            font-size: 0.8rem;
            color: #64748b;
            margin: 1rem 0;
            line-height: 1.4;
        }
        
        .resume-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .btn-primary-small {
            background: #667eea;
            color: white;
        }
        
        .btn-primary-small:hover {
            background: #5a67d8;
        }
        
        .btn-secondary-small {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-secondary-small:hover {
            background: #e2e8f0;
        }
        
        .btn-danger-small {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .btn-danger-small:hover {
            background: #fecaca;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            font-size: 0.9rem;
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
                    <li class="active">
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
                        <span class="user-email"></span>
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
                    <h1>My Resumes</h1>
                </div>
                <div class="header-right">
                    <a href="resume_form.html" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create New Resume
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Filter Bar -->
                <div class="filter-bar">
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="completed">Completed</option>
                        <option value="published">Published</option>
                    </select>
                    
                    <select class="filter-select" id="templateFilter">
                        <option value="">All Templates</option>
                        <option value="Modern Professional">Modern Professional</option>
                        <option value="Creative Design">Creative Design</option>
                        <option value="Minimal Clean">Minimal Clean</option>
                    </select>
                    
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search resumes..." id="resumeSearch">
                    </div>
                </div>

                <!-- Resumes Grid -->
                <?php if (empty($resumes)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h3>No resumes yet</h3>
                        <p>Create your first resume to get started!</p>
                        <a href="resume_form.html" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i>
                            Create Resume
                        </a>
                    </div>
                <?php else: ?>
                    <div class="resumes-grid" id="resumesGrid">
                        <?php foreach ($resumes as $resume): ?>
                        <div class="resume-card" data-status="<?php echo $resume['status']; ?>" data-template="<?php echo htmlspecialchars($resume['template_name']); ?>">
                            <div class="resume-header">
                                <div>
                                    <div class="resume-title"><?php echo htmlspecialchars($resume['resume_name']); ?></div>
                                    <div class="resume-template"><?php echo htmlspecialchars($resume['template_name']); ?></div>
                                </div>
                                <span class="resume-status status-<?php echo $resume['status']; ?>">
                                    <?php echo ucfirst($resume['status']); ?>
                                </span>
                            </div>
                            
                            <div class="resume-dates">
                                <div><strong>Created:</strong> <?php echo date('M j, Y', strtotime($resume['created_at'])); ?></div>
                                <div><strong>Modified:</strong> <?php echo date('M j, Y', strtotime($resume['updated_at'])); ?></div>
                            </div>
                            
                            <div class="resume-actions">
                                <a href="resume_display.php?id=<?php echo $resume['id']; ?>" class="btn-small btn-primary-small">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="preview.php?id=<?php echo $resume['id']; ?>" class="btn-small btn-secondary-small">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a href="download.php?id=<?php echo $resume['id']; ?>" class="btn-small btn-secondary-small">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <button class="btn-small btn-danger-small" onclick="deleteResume(<?php echo $resume['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        // Additional JavaScript for My Resumes page
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const templateFilter = document.getElementById('templateFilter');
            const resumeSearch = document.getElementById('resumeSearch');
            const resumesGrid = document.getElementById('resumesGrid');

            function filterResumes() {
                const statusValue = statusFilter.value;
                const templateValue = templateFilter.value;
                const searchValue = resumeSearch.value.toLowerCase();
                const resumeCards = resumesGrid.querySelectorAll('.resume-card');

                resumeCards.forEach(card => {
                    const cardStatus = card.getAttribute('data-status');
                    const cardTemplate = card.getAttribute('data-template');
                    const cardTitle = card.querySelector('.resume-title').textContent.toLowerCase();

                    const statusMatch = !statusValue || cardStatus === statusValue;
                    const templateMatch = !templateValue || cardTemplate === templateValue;
                    const searchMatch = !searchValue || cardTitle.includes(searchValue);

                    if (statusMatch && templateMatch && searchMatch) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            statusFilter.addEventListener('change', filterResumes);
            templateFilter.addEventListener('change', filterResumes);
            resumeSearch.addEventListener('input', filterResumes);
        });

        function deleteResume(resumeId) {
            if (confirm('Are you sure you want to delete this resume? This action cannot be undone.')) {
                // Show loading state
                const card = document.querySelector(`[data-resume-id="${resumeId}"]`);
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.pointerEvents = 'none';
                }

                // Make AJAX request to delete resume
                fetch('delete_resume.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ resume_id: resumeId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the card from DOM
                        if (card) {
                            card.remove();
                        }
                        showNotification('Resume deleted successfully', 'success');
                    } else {
                        // Restore card state
                        if (card) {
                            card.style.opacity = '1';
                            card.style.pointerEvents = 'auto';
                        }
                        showNotification('Error deleting resume', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Restore card state
                    if (card) {
                        card.style.opacity = '1';
                        card.style.pointerEvents = 'auto';
                    }
                    showNotification('Error deleting resume', 'error');
                });
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>
