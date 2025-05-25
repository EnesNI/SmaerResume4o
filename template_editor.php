<?php
session_start();

// Get template ID and user data
$template_id = $_GET['template'] ?? 1;
$resume_id = $_GET['resume_id'] ?? null;

// Database connection
$host = 'localhost';
$dbname = 'resume_builder';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $pdo = null;
}

// Load existing resume data if editing
$resume_data = [];
if ($resume_id && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM resume_data WHERE resume_id = ?");
        $stmt->execute([$resume_id]);
        $existing_data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing_data) {
            $resume_data = json_decode($existing_data['data'], true);
        }
    } catch(PDOException $e) {
        // Continue with empty data
    }
}

// Default empty data structure
if (empty($resume_data)) {
    $resume_data = [
        'full_name' => '',
        'title' => '',
        'email' => '',
        'phone' => '',
        'location' => '',
        'linkedin' => '',
        'website' => '',
        'summary' => '',
        'experience' => [
            ['title' => '', 'company' => '', 'duration' => '', 'description' => '']
        ],
        'education' => [
            ['degree' => '', 'school' => '', 'year' => '']
        ],
        'skills' => ['']
    ];
}

// Template names
$template_names = [
    1 => 'Modern Professional',
    2 => 'Creative Design', 
    3 => 'Minimal Clean',
    4 => 'Executive Style'
];

$template_name = $template_names[$template_id] ?? 'Modern Professional';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resume - <?php echo $template_name; ?></title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .editor-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            height: calc(100vh - 80px);
            padding: 2rem;
        }

        .editor-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .panel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-section:last-child {
            border-bottom: none;
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
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .dynamic-section {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .remove-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .add-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .add-btn:hover {
            background: #059669;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .skill-tag {
            background: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .skill-remove {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            cursor: pointer;
            font-size: 0.7rem;
        }

        .preview-panel {
            background: #f8fafc;
        }

        .preview-content {
            height: 100%;
            overflow-y: auto;
        }

        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-save {
            background: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background: #059669;
        }

        .btn-preview {
            background: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-preview:hover {
            background: #5a67d8;
        }

        .btn-download {
            background: #f59e0b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background: #d97706;
        }

        @media (max-width: 1024px) {
            .editor-container {
                grid-template-columns: 1fr;
                height: auto;
            }
            
            .preview-panel {
                order: -1;
                height: 400px;
            }
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
                    <li class="active">
                        <a href="resume_form.html">
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
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Edit Resume - <?php echo $template_name; ?></h1>
                </div>
                <div class="header-right">
                    <a href="templates.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Templates
                    </a>
                </div>
            </header>

            <div class="editor-container">
                <!-- Editor Panel -->
                <div class="editor-panel">
                    <div class="panel-header">
                        <i class="fas fa-edit"></i>
                        Resume Editor
                    </div>
                    <div class="panel-content">
                        <form id="resumeForm">
                            <!-- Personal Information -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user"></i>
                                    Personal Information
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-input" name="full_name" value="<?php echo htmlspecialchars($resume_data['full_name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Professional Title</label>
                                    <input type="text" class="form-input" name="title" value="<?php echo htmlspecialchars($resume_data['title']); ?>" placeholder="e.g., Software Engineer">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-input" name="email" value="<?php echo htmlspecialchars($resume_data['email']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-input" name="phone" value="<?php echo htmlspecialchars($resume_data['phone']); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-input" name="location" value="<?php echo htmlspecialchars($resume_data['location']); ?>" placeholder="City, State">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" class="form-input" name="linkedin" value="<?php echo htmlspecialchars($resume_data['linkedin']); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Website/Portfolio</label>
                                    <input type="url" class="form-input" name="website" value="<?php echo htmlspecialchars($resume_data['website']); ?>">
                                </div>
                            </div>

                            <!-- Professional Summary -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-align-left"></i>
                                    Professional Summary
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">Summary</label>
                                    <textarea class="form-textarea" name="summary" rows="4" placeholder="Write a brief professional summary..."><?php echo htmlspecialchars($resume_data['summary']); ?></textarea>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-briefcase"></i>
                                    Work Experience
                                </h3>
                                
                                <div id="experienceContainer">
                                    <?php foreach ($resume_data['experience'] as $index => $exp): ?>
                                    <div class="dynamic-section experience-item">
                                        <?php if ($index > 0): ?>
                                            <button type="button" class="remove-btn" onclick="removeExperience(this)">×</button>
                                        <?php endif; ?>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Job Title</label>
                                            <input type="text" class="form-input" name="experience[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($exp['title']); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Company</label>
                                            <input type="text" class="form-input" name="experience[<?php echo $index; ?>][company]" value="<?php echo htmlspecialchars($exp['company']); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Duration</label>
                                            <input type="text" class="form-input" name="experience[<?php echo $index; ?>][duration]" value="<?php echo htmlspecialchars($exp['duration']); ?>" placeholder="e.g., 2020 - Present">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-textarea" name="experience[<?php echo $index; ?>][description]" rows="3"><?php echo htmlspecialchars($exp['description']); ?></textarea>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <button type="button" class="add-btn" onclick="addExperience()">
                                    <i class="fas fa-plus"></i> Add Experience
                                </button>
                            </div>

                            <!-- Education -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    Education
                                </h3>
                                
                                <div id="educationContainer">
                                    <?php foreach ($resume_data['education'] as $index => $edu): ?>
                                    <div class="dynamic-section education-item">
                                        <?php if ($index > 0): ?>
                                            <button type="button" class="remove-btn" onclick="removeEducation(this)">×</button>
                                        <?php endif; ?>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Degree</label>
                                            <input type="text" class="form-input" name="education[<?php echo $index; ?>][degree]" value="<?php echo htmlspecialchars($edu['degree']); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">School/University</label>
                                            <input type="text" class="form-input" name="education[<?php echo $index; ?>][school]" value="<?php echo htmlspecialchars($edu['school']); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Year</label>
                                            <input type="text" class="form-input" name="education[<?php echo $index; ?>][year]" value="<?php echo htmlspecialchars($edu['year']); ?>" placeholder="e.g., 2020">
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <button type="button" class="add-btn" onclick="addEducation()">
                                    <i class="fas fa-plus"></i> Add Education
                                </button>
                            </div>

                            <!-- Skills -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-cogs"></i>
                                    Skills
                                </h3>
                                
                                <div class="skills-container" id="skillsContainer">
                                    <?php foreach ($resume_data['skills'] as $skill): ?>
                                        <?php if (!empty($skill)): ?>
                                        <div class="skill-tag">
                                            <?php echo htmlspecialchars($skill); ?>
                                            <button type="button" class="skill-remove" onclick="removeSkill(this)">×</button>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Add Skill</label>
                                    <input type="text" class="form-input" id="skillInput" placeholder="Type a skill and press Enter">
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="button" class="btn-save" onclick="saveResume()">
                            <i class="fas fa-save"></i> Save Resume
                        </button>
                        <button type="button" class="btn-preview" onclick="updatePreview()">
                            <i class="fas fa-eye"></i> Update Preview
                        </button>
                        <button type="button" class="btn-download" onclick="downloadResume()">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </div>
                </div>

                <!-- Preview Panel -->
                <div class="editor-panel preview-panel">
                    <div class="panel-header">
                        <i class="fas fa-eye"></i>
                        Live Preview
                    </div>
                    <div class="preview-content">
                        <iframe id="previewFrame" class="preview-iframe" src="template_preview.php?template=<?php echo $template_id; ?>"></iframe>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        let experienceCount = <?php echo count($resume_data['experience']); ?>;
        let educationCount = <?php echo count($resume_data['education']); ?>;

        // Add experience section
        function addExperience() {
            const container = document.getElementById('experienceContainer');
            const newSection = document.createElement('div');
            newSection.className = 'dynamic-section experience-item';
            newSection.innerHTML = `
                <button type="button" class="remove-btn" onclick="removeExperience(this)">×</button>
                <div class="form-group">
                    <label class="form-label">Job Title</label>
                    <input type="text" class="form-input" name="experience[${experienceCount}][title]">
                </div>
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-input" name="experience[${experienceCount}][company]">
                </div>
                <div class="form-group">
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-input" name="experience[${experienceCount}][duration]" placeholder="e.g., 2020 - Present">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" name="experience[${experienceCount}][description]" rows="3"></textarea>
                </div>
            `;
            container.appendChild(newSection);
            experienceCount++;
        }

        // Remove experience section
        function removeExperience(button) {
            button.parentElement.remove();
        }

        // Add education section
        function addEducation() {
            const container = document.getElementById('educationContainer');
            const newSection = document.createElement('div');
            newSection.className = 'dynamic-section education-item';
            newSection.innerHTML = `
                <button type="button" class="remove-btn" onclick="removeEducation(this)">×</button>
                <div class="form-group">
                    <label class="form-label">Degree</label>
                    <input type="text" class="form-input" name="education[${educationCount}][degree]">
                </div>
                <div class="form-group">
                    <label class="form-label">School/University</label>
                    <input type="text" class="form-input" name="education[${educationCount}][school]">
                </div>
                <div class="form-group">
                    <label class="form-label">Year</label>
                    <input type="text" class="form-input" name="education[${educationCount}][year]" placeholder="e.g., 2020">
                </div>
            `;
            container.appendChild(newSection);
            educationCount++;
        }

        // Remove education section
        function removeEducation(button) {
            button.parentElement.remove();
        }

        // Add skill
        document.getElementById('skillInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const skill = this.value.trim();
                if (skill) {
                    addSkill(skill);
                    this.value = '';
                }
            }
        });

        function addSkill(skillName) {
            const container = document.getElementById('skillsContainer');
            const skillTag = document.createElement('div');
            skillTag.className = 'skill-tag';
            skillTag.innerHTML = `
                ${skillName}
                <button type="button" class="skill-remove" onclick="removeSkill(this)">×</button>
            `;
            container.appendChild(skillTag);
        }

        // Remove skill
        function removeSkill(button) {
            button.parentElement.remove();
        }

        // Update preview
        function updatePreview() {
            const formData = new FormData(document.getElementById('resumeForm'));
            
            // Collect skills
            const skills = [];
            document.querySelectorAll('.skill-tag').forEach(tag => {
                const skillText = tag.textContent.replace('×', '').trim();
                if (skillText) skills.push(skillText);
            });
            
            // Create data object
            const data = {
                full_name: formData.get('full_name'),
                title: formData.get('title'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                location: formData.get('location'),
                linkedin: formData.get('linkedin'),
                website: formData.get('website'),
                summary: formData.get('summary'),
                experience: [],
                education: [],
                skills: skills
            };

            // Collect experience
            const experienceItems = document.querySelectorAll('.experience-item');
            experienceItems.forEach((item, index) => {
                const title = item.querySelector(`input[name="experience[${index}][title]"]`)?.value || '';
                const company = item.querySelector(`input[name="experience[${index}][company]"]`)?.value || '';
                const duration = item.querySelector(`input[name="experience[${index}][duration]"]`)?.value || '';
                const description = item.querySelector(`textarea[name="experience[${index}][description]"]`)?.value || '';
                
                if (title || company) {
                    data.experience.push({ title, company, duration, description });
                }
            });

            // Collect education
            const educationItems = document.querySelectorAll('.education-item');
            educationItems.forEach((item, index) => {
                const degree = item.querySelector(`input[name="education[${index}][degree]"]`)?.value || '';
                const school = item.querySelector(`input[name="education[${index}][school]"]`)?.value || '';
                const year = item.querySelector(`input[name="education[${index}][year]"]`)?.value || '';
                
                if (degree || school) {
                    data.education.push({ degree, school, year });
                }
            });

            // Update preview iframe
            const iframe = document.getElementById('previewFrame');
            const templateId = <?php echo $template_id; ?>;
            iframe.src = `template_preview.php?template=${templateId}&data=${encodeURIComponent(JSON.stringify(data))}`;
        }

        // Save resume
        function saveResume() {
            updatePreview(); // Update preview first
            
            const formData = new FormData(document.getElementById('resumeForm'));
            
            // Show loading state
            const saveBtn = document.querySelector('.btn-save');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Here you would send the data to your PHP backend
            fetch('save_resume.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Resume saved successfully!', 'success');
                } else {
                    showNotification('Error saving resume', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving resume', 'error');
            })
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        }

        // Download resume
        function downloadResume() {
            updatePreview();
            
            // Create download link
            const templateId = <?php echo $template_id; ?>;
            const downloadUrl = `download_resume.php?template=${templateId}`;
            
            // Open in new window for PDF generation
            window.open(downloadUrl, '_blank');
        }

        // Auto-update preview on form changes
        document.getElementById('resumeForm').addEventListener('input', function() {
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(updatePreview, 1000);
        });

        // Initial preview update
        updatePreview();

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
