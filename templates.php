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
    
    // Get templates from database
    $stmt = $pdo->query("SELECT * FROM templates ORDER BY is_premium ASC, template_name ASC");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Fallback to mock data if database connection fails
    $templates = [
        [
            'id' => 1,
            'template_name' => 'Modern Professional',
            'template_description' => 'Clean and modern design perfect for tech professionals',
            'is_premium' => 0,
            'preview_image' => '/placeholder.svg?height=300&width=200'
        ],
        [
            'id' => 2,
            'template_name' => 'Creative Design',
            'template_description' => 'Colorful and creative layout for designers and marketers',
            'is_premium' => 0,
            'preview_image' => '/placeholder.svg?height=300&width=200'
        ],
        [
            'id' => 3,
            'template_name' => 'Minimal Clean',
            'template_description' => 'Simple and elegant design that works for any industry',
            'is_premium' => 0,
            'preview_image' => '/placeholder.svg?height=300&width=200'
        ],
        [
            'id' => 4,
            'template_name' => 'Executive Style',
            'template_description' => 'Professional layout for senior positions and executives',
            'is_premium' => 1,
            'preview_image' => '/placeholder.svg?height=300&width=200'
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
    <title>Templates - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .template-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .template-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .template-preview {
            height: 200px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .template-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .premium-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .template-content {
            padding: 1.5rem;
        }
        
        .template-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .template-description {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        
        .template-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-template {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .btn-primary-template {
            background: #667eea;
            color: white;
        }
        
        .btn-primary-template:hover {
            background: #5a67d8;
        }
        
        .btn-secondary-template {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary-template:hover {
            background: #e2e8f0;
        }
        
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-tab {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .filter-tab.active,
        .filter-tab:hover {
            background: #667eea;
            color: white;
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
                    <li class="active">
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
                        <span class="user-email">john@example.com</span>
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
                    <h1>Resume Templates</h1>
                </div>
                <div class="header-right">
                    <a href="resume_form.html" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create Resume
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Filter Bar -->
                <div class="filter-bar">
                    <div class="filter-tabs">
                        <a href="#" class="filter-tab active" data-filter="all">All Templates</a>
                        <a href="#" class="filter-tab" data-filter="free">Free</a>
                        <a href="#" class="filter-tab" data-filter="premium">Premium</a>
                    </div>
                    
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search templates..." id="templateSearch">
                    </div>
                </div>

                <!-- Templates Grid -->
                <div class="templates-grid" id="templatesGrid">
                    <?php foreach ($templates as $template): ?>
                    <div class="template-card" data-type="<?php echo $template['is_premium'] ? 'premium' : 'free'; ?>">
                        <div class="template-preview">
                            <img src="<?php echo $template['preview_image'] ?? '/placeholder.svg?height=200&width=280'; ?>" alt="<?php echo htmlspecialchars($template['template_name']); ?>">
                            <?php if ($template['is_premium']): ?>
                                <div class="premium-badge">
                                    <i class="fas fa-crown"></i> Premium
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="template-content">
                            <h3 class="template-title"><?php echo htmlspecialchars($template['template_name']); ?></h3>
                            <p class="template-description"><?php echo htmlspecialchars($template['template_description']); ?></p>
                            
                            <div class="template-actions">
                                <a href="template_editor.php?template=<?php echo $template['id']; ?>" class="btn-template btn-primary-template">
                                    <i class="fas fa-plus"></i> Use Template
                                </a>
                                <a href="template_preview.php?template=<?php echo $template['id']; ?>&preview=1" class="btn-template btn-secondary-template">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        // Template filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const templateSearch = document.getElementById('templateSearch');
            const templatesGrid = document.getElementById('templatesGrid');

            function filterTemplates() {
                const activeFilter = document.querySelector('.filter-tab.active').getAttribute('data-filter');
                const searchValue = templateSearch.value.toLowerCase();
                const templateCards = templatesGrid.querySelectorAll('.template-card');

                templateCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');
                    const cardTitle = card.querySelector('.template-title').textContent.toLowerCase();
                    const cardDescription = card.querySelector('.template-description').textContent.toLowerCase();

                    const typeMatch = activeFilter === 'all' || cardType === activeFilter;
                    const searchMatch = !searchValue || 
                        cardTitle.includes(searchValue) || 
                        cardDescription.includes(searchValue);

                    if (typeMatch && searchMatch) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    filterTemplates();
                });
            });

            templateSearch.addEventListener('input', filterTemplates);
        });
    </script>
</body>
</html>
