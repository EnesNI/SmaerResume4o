<?php
session_start();

$template_id = $_GET['template'] ?? 1;
$resume_id = $_GET['resume_id'] ?? null;
$from_analysis = $_GET['from_analysis'] ?? false;

// Get analysis data if coming from analysis page
$analysis_data = null;
if ($from_analysis) {
    $analysis_data = [
        'match_score' => $_GET['match_score'] ?? 0,
        'ai_score' => $_GET['ai_score'] ?? 0,
        'missing_keywords' => explode(',', $_GET['missing_keywords'] ?? '')
    ];
}

// Sample resume data for demo
$resume_data = [
    'full_name' => 'John Smith',
    'title' => 'Software Engineer',
    'email' => 'john.smith@email.com',
    'phone' => '+1 (555) 123-4567',
    'location' => 'San Francisco, CA',
    'linkedin' => 'linkedin.com/in/johnsmith',
    'summary' => 'Experienced Software Engineer with 5+ years of expertise in full-stack development.',
    'experience' => [
        [
            'title' => 'Senior Software Engineer',
            'company' => 'Tech Solutions Inc.',
            'duration' => '2022 - Present',
            'description' => 'Led development of microservices architecture serving 1M+ users.'
        ]
    ],
    'education' => [
        [
            'degree' => 'Bachelor of Science in Computer Science',
            'school' => 'University of California, Berkeley',
            'year' => '2020'
        ]
    ],
    'skills' => ['JavaScript', 'React', 'Node.js', 'Python', 'AWS']
];

$user_name = $_SESSION['user_name'] ?? 'John Doe';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Resume Editor - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .editor-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            height: calc(100vh - 80px);
            padding: 2rem;
        }

        .resume-preview {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .resume-content {
            padding: 2rem;
            height: 100%;
            overflow-y: auto;
        }

        .editable-section {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 0.5rem;
            margin: 0.25rem 0;
        }

        .editable-section:hover {
            background: rgba(102, 126, 234, 0.1);
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.3);
        }

        .editable-section.active {
            background: rgba(102, 126, 234, 0.15);
            box-shadow: 0 0 0 2px #667eea;
        }

        .edit-indicator {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .editable-section:hover .edit-indicator {
            opacity: 1;
        }

        .ai-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .panel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .section-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .ai-suggestions {
            margin-top: 1.5rem;
        }

        .suggestion-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .suggestion-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .suggestion-icon {
            width: 24px;
            height: 24px;
            background: #10b981;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .suggestion-title {
            font-weight: 600;
            color: #166534;
            font-size: 0.9rem;
        }

        .suggestion-text {
            color: #15803d;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .suggestion-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-apply {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-apply:hover {
            background: #059669;
        }

        .btn-regenerate {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-regenerate:hover {
            background: #e2e8f0;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        /* Resume styling */
        .resume-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin: -2rem -2rem 2rem -2rem;
        }

        .name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .job-title {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 1rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .experience-item {
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            border-left: 3px solid #667eea;
        }

        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .skill-tag {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        @media (max-width: 1024px) {
            .editor-container {
                grid-template-columns: 1fr;
                height: auto;
            }
            
            .ai-panel {
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
                    <h1>Interactive Resume Editor</h1>
                </div>
                <div class="header-right">
                    <button class="btn btn-secondary" onclick="saveResume()">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                    <button class="btn btn-primary" onclick="downloadResume()">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                </div>
            </header>

            <div class="editor-container">
                <!-- Resume Preview -->
                <div class="resume-preview">
                    <div class="resume-content">
                        <!-- Header Section -->
                        <div class="resume-header">
                            <div class="editable-section" data-section="name" data-type="text">
                                <div class="name"><?php echo htmlspecialchars($resume_data['full_name']); ?></div>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                            
                            <div class="editable-section" data-section="title" data-type="text">
                                <div class="job-title"><?php echo htmlspecialchars($resume_data['title']); ?></div>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                            
                            <div class="editable-section" data-section="contact" data-type="contact">
                                <div class="contact-info">
                                    <span><?php echo htmlspecialchars($resume_data['email']); ?></span>
                                    <span><?php echo htmlspecialchars($resume_data['phone']); ?></span>
                                    <span><?php echo htmlspecialchars($resume_data['location']); ?></span>
                                    <span><?php echo htmlspecialchars($resume_data['linkedin']); ?></span>
                                </div>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="section">
                            <h3>Professional Summary</h3>
                            <div class="editable-section" data-section="summary" data-type="paragraph">
                                <p><?php echo htmlspecialchars($resume_data['summary']); ?></p>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                        </div>

                        <!-- Experience Section -->
                        <div class="section">
                            <h3>Professional Experience</h3>
                            <?php foreach ($resume_data['experience'] as $index => $exp): ?>
                            <div class="experience-item">
                                <div class="editable-section" data-section="experience-title" data-type="text" data-index="<?php echo $index; ?>">
                                    <h4><?php echo htmlspecialchars($exp['title']); ?></h4>
                                    <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                                </div>
                                
                                <div class="editable-section" data-section="experience-company" data-type="text" data-index="<?php echo $index; ?>">
                                    <p><strong><?php echo htmlspecialchars($exp['company']); ?></strong> | <?php echo htmlspecialchars($exp['duration']); ?></p>
                                    <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                                </div>
                                
                                <div class="editable-section" data-section="experience-description" data-type="paragraph" data-index="<?php echo $index; ?>">
                                    <p><?php echo htmlspecialchars($exp['description']); ?></p>
                                    <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Education Section -->
                        <div class="section">
                            <h3>Education</h3>
                            <?php foreach ($resume_data['education'] as $index => $edu): ?>
                            <div class="editable-section" data-section="education" data-type="education" data-index="<?php echo $index; ?>">
                                <h4><?php echo htmlspecialchars($edu['degree']); ?></h4>
                                <p><?php echo htmlspecialchars($edu['school']); ?> | <?php echo htmlspecialchars($edu['year']); ?></p>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Skills Section -->
                        <div class="section">
                            <h3>Skills</h3>
                            <div class="editable-section" data-section="skills" data-type="skills">
                                <div class="skills-grid">
                                    <?php foreach ($resume_data['skills'] as $skill): ?>
                                    <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="edit-indicator"><i class="fas fa-edit"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Panel -->
                <div class="ai-panel">
                    <div class="panel-header">
                        <i class="fas fa-robot"></i>
                        AI Assistant
                    </div>
                    <div class="panel-content" id="aiPanelContent">
                        <div class="empty-state">
                            <i class="fas fa-mouse-pointer"></i>
                            <h3>Click on any section</h3>
                            <p>Click on any text in your resume to get AI-powered suggestions and improvements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        let currentSection = null;
        let isLoading = false;

        // Load analysis data if available
        let analysisData = null;
        if (sessionStorage.getItem('resumeAnalysisData')) {
            analysisData = JSON.parse(sessionStorage.getItem('resumeAnalysisData'));
            
            // Show analysis-based suggestions on load
            if (analysisData.matchScore < 70) {
                setTimeout(() => {
                    showAnalysisBasedSuggestions();
                }, 1000);
            }
        }

        function showAnalysisBasedSuggestions() {
            const panelContent = document.getElementById('aiPanelContent');
            
            panelContent.innerHTML = `
                <div class="section-info" style="border-left: 4px solid #f59e0b;">
                    <div class="section-title">
                        <i class="fas fa-chart-line"></i>
                        Analysis Results
                    </div>
                    <div class="section-description">
                        Based on your resume analysis, here are priority improvements to boost your match score from ${analysisData.matchScore}% to 80%+.
                    </div>
                </div>
                <div class="ai-suggestions">
                    <h4 style="margin-bottom: 1rem; color: #1e293b; font-size: 1rem;">
                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                        Priority Improvements
                    </h4>
                    ${generateAnalysisBasedSuggestions()}
                </div>
            `;
        }

        function generateAnalysisBasedSuggestions() {
            let suggestions = '';
            
            if (analysisData.matchScore < 70) {
                suggestions += `
                    <div class="suggestion-card" style="border-left: 4px solid #ef4444;">
                        <div class="suggestion-header">
                            <div class="suggestion-icon" style="background: #ef4444;">!</div>
                            <div class="suggestion-title">Low Match Score (${analysisData.matchScore}%)</div>
                        </div>
                        <div class="suggestion-text">Your resume needs significant improvements to match the job requirements. Focus on adding missing keywords and relevant experience.</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="highlightLowMatchSections()">
                                <i class="fas fa-search"></i> Show Problem Areas
                            </button>
                        </div>
                    </div>
                `;
            }
            
            if (analysisData.aiScore > 50) {
                suggestions += `
                    <div class="suggestion-card" style="border-left: 4px solid #f59e0b;">
                        <div class="suggestion-header">
                            <div class="suggestion-icon" style="background: #f59e0b;">AI</div>
                            <div class="suggestion-title">High AI Detection (${analysisData.aiScore}%)</div>
                        </div>
                        <div class="suggestion-text">Your resume shows signs of AI-generated content. Make it more personal and authentic.</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="highlightAISections()">
                                <i class="fas fa-robot"></i> Fix AI Content
                            </button>
                        </div>
                    </div>
                `;
            }
            
            if (analysisData.missingKeywords && analysisData.missingKeywords.length > 0) {
                suggestions += `
                    <div class="suggestion-card" style="border-left: 4px solid #10b981;">
                        <div class="suggestion-header">
                            <div class="suggestion-icon" style="background: #10b981;">+</div>
                            <div class="suggestion-title">Add Missing Keywords</div>
                        </div>
                        <div class="suggestion-text">Add these important keywords: ${analysisData.missingKeywords.slice(0, 5).join(', ')}</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="addMissingKeywords()">
                                <i class="fas fa-plus"></i> Add Keywords
                            </button>
                        </div>
                    </div>
                `;
            }
            
            return suggestions;
        }

        function highlightLowMatchSections() {
            // Highlight sections that need improvement
            const sections = ['summary', 'experience-description', 'skills'];
            sections.forEach(section => {
                const elements = document.querySelectorAll(`[data-section="${section}"]`);
                elements.forEach(el => {
                    el.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    el.style.animation = 'pulse 2s infinite';
                });
            });
            
            showNotification('Problem areas highlighted in red', 'info');
        }

        function highlightAISections() {
            // Highlight sections that might be AI-generated
            const sections = ['summary', 'experience-description'];
            sections.forEach(section => {
                const elements = document.querySelectorAll(`[data-section="${section}"]`);
                elements.forEach(el => {
                    el.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.5)';
                    el.style.animation = 'pulse 2s infinite';
                });
            });
            
            showNotification('AI-generated sections highlighted in orange', 'info');
        }

        function addMissingKeywords() {
            // Auto-select skills section and show keyword suggestions
            const skillsSection = document.querySelector('[data-section="skills"]');
            if (skillsSection) {
                selectSection(skillsSection);
                showNotification('Skills section selected. Add the suggested keywords.', 'success');
            }
        }

        // Add click handlers to editable sections
        document.addEventListener('DOMContentLoaded', function() {
            const editableSections = document.querySelectorAll('.editable-section');
            
            editableSections.forEach(section => {
                section.addEventListener('click', function() {
                    selectSection(this);
                });
            });
        });

        function selectSection(element) {
            // Remove active class from all sections
            document.querySelectorAll('.editable-section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to selected section
            element.classList.add('active');
            currentSection = element;
            
            // Get section info
            const sectionType = element.dataset.section;
            const dataType = element.dataset.type;
            const index = element.dataset.index;
            
            // Show AI suggestions for this section
            showAISuggestions(sectionType, dataType, index, element);
        }

        function showAISuggestions(sectionType, dataType, index, element) {
            const panelContent = document.getElementById('aiPanelContent');
            
            // Show loading state
            panelContent.innerHTML = `
                <div class="section-info">
                    <div class="section-title">
                        <i class="fas fa-${getSectionIcon(sectionType)}"></i>
                        ${getSectionTitle(sectionType)}
                    </div>
                    <div class="section-description">
                        ${getSectionDescription(sectionType)}
                    </div>
                </div>
                <div class="ai-suggestions">
                    <div style="text-align: center; padding: 2rem;">
                        <div class="loading-spinner"></div>
                        <p style="margin-top: 1rem; color: #64748b;">Generating AI suggestions...</p>
                    </div>
                </div>
            `;

            // Simulate AI processing delay
            setTimeout(() => {
                const suggestions = generateSuggestions(sectionType, dataType, element);
                displaySuggestions(sectionType, suggestions);
            }, 1500);
        }

        function getSectionIcon(sectionType) {
            const icons = {
                'name': 'user',
                'title': 'briefcase',
                'contact': 'address-card',
                'summary': 'align-left',
                'experience-title': 'briefcase',
                'experience-company': 'building',
                'experience-description': 'list-ul',
                'education': 'graduation-cap',
                'skills': 'cogs'
            };
            return icons[sectionType] || 'edit';
        }

        function getSectionTitle(sectionType) {
            const titles = {
                'name': 'Full Name',
                'title': 'Professional Title',
                'contact': 'Contact Information',
                'summary': 'Professional Summary',
                'experience-title': 'Job Title',
                'experience-company': 'Company & Duration',
                'experience-description': 'Job Description',
                'education': 'Education',
                'skills': 'Skills'
            };
            return titles[sectionType] || 'Section';
        }

        function getSectionDescription(sectionType) {
            const descriptions = {
                'name': 'Your full name as it should appear on professional documents.',
                'title': 'A concise title that summarizes your professional role.',
                'contact': 'Essential contact information for potential employers.',
                'summary': 'A brief overview of your professional background and key strengths.',
                'experience-title': 'Your official job title or role.',
                'experience-company': 'Company name and employment duration.',
                'experience-description': 'Key responsibilities and achievements in this role.',
                'education': 'Your educational background and qualifications.',
                'skills': 'Technical and soft skills relevant to your target role.'
            };
            return descriptions[sectionType] || 'Click to edit this section.';
        }

        function generateSuggestions(sectionType, dataType, element) {
            // Mock AI suggestions based on section type
            const suggestions = {
                'name': [
                    {
                        title: 'Professional Format',
                        text: 'Consider using "John A. Smith" format for a more professional appearance.',
                        newText: 'John A. Smith'
                    }
                ],
                'title': [
                    {
                        title: 'Add Seniority Level',
                        text: 'Include your experience level to immediately convey your expertise.',
                        newText: 'Senior Software Engineer'
                    },
                    {
                        title: 'Specify Technology',
                        text: 'Mention your primary technology stack to attract relevant opportunities.',
                        newText: 'Full-Stack Software Engineer (React/Node.js)'
                    }
                ],
                'summary': [
                    {
                        title: 'Add Quantifiable Achievements',
                        text: 'Include specific numbers and metrics to demonstrate your impact.',
                        newText: 'Experienced Software Engineer with 5+ years of expertise in full-stack development, successfully delivering 15+ web applications and improving system performance by 40%.'
                    },
                    {
                        title: 'Highlight Key Technologies',
                        text: 'Mention your core technical skills early in the summary.',
                        newText: 'Experienced Software Engineer specializing in React, Node.js, and AWS with 5+ years of expertise in building scalable web applications.'
                    }
                ],
                'experience-description': [
                    {
                        title: 'Use Action Verbs',
                        text: 'Start with strong action verbs to make your achievements more impactful.',
                        newText: 'Architected and implemented microservices architecture serving 1M+ users, resulting in 50% improved system scalability and 30% reduced response times.'
                    },
                    {
                        title: 'Add Business Impact',
                        text: 'Connect your technical work to business outcomes.',
                        newText: 'Led development of microservices architecture serving 1M+ users, contributing to 25% increase in user engagement and $2M annual revenue growth.'
                    }
                ],
                'skills': [
                    {
                        title: 'Add Trending Technologies',
                        text: 'Include current in-demand technologies relevant to your field.',
                        newText: 'JavaScript, React, Node.js, Python, AWS, Docker, Kubernetes, TypeScript, GraphQL'
                    },
                    {
                        title: 'Organize by Category',
                        text: 'Group skills by category for better readability.',
                        newText: 'Frontend: React, JavaScript, TypeScript, HTML/CSS | Backend: Node.js, Python, Express | Cloud: AWS, Docker, Kubernetes'
                    }
                ]
            };

            return suggestions[sectionType] || [
                {
                    title: 'Improve Content',
                    text: 'Consider making this section more specific and impactful.',
                    newText: 'Enhanced version of your content...'
                }
            ];
        }

        function displaySuggestions(sectionType, suggestions) {
            const panelContent = document.getElementById('aiPanelContent');
            
            let suggestionsHtml = `
                <div class="section-info">
                    <div class="section-title">
                        <i class="fas fa-${getSectionIcon(sectionType)}"></i>
                        ${getSectionTitle(sectionType)}
                    </div>
                    <div class="section-description">
                        ${getSectionDescription(sectionType)}
                    </div>
                </div>
                <div class="ai-suggestions">
                    <h4 style="margin-bottom: 1rem; color: #1e293b; font-size: 1rem;">
                        <i class="fas fa-lightbulb" style="color: #fbbf24;"></i>
                        AI Suggestions
                    </h4>
            `;

            suggestions.forEach((suggestion, index) => {
                suggestionsHtml += `
                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-icon">${index + 1}</div>
                            <div class="suggestion-title">${suggestion.title}</div>
                        </div>
                        <div class="suggestion-text">${suggestion.text}</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="applySuggestion('${suggestion.newText}')">
                                <i class="fas fa-check"></i> Apply
                            </button>
                            <button class="btn-regenerate" onclick="regenerateSuggestion(${index})">
                                <i class="fas fa-sync"></i> Regenerate
                            </button>
                        </div>
                    </div>
                `;
            });

            suggestionsHtml += '</div>';
            panelContent.innerHTML = suggestionsHtml;
        }

        function applySuggestion(newText) {
            if (!currentSection) return;
            
            // Find the text content in the current section and update it
            const textElement = currentSection.querySelector('h4, p, .name, .job-title');
            if (textElement) {
                textElement.textContent = newText;
                
                // Show success feedback
                showNotification('Suggestion applied successfully!', 'success');
                
                // Refresh suggestions for the updated content
                const sectionType = currentSection.dataset.section;
                const dataType = currentSection.dataset.type;
                const index = currentSection.dataset.index;
                
                setTimeout(() => {
                    showAISuggestions(sectionType, dataType, index, currentSection);
                }, 1000);
            }
        }

        function regenerateSuggestion(index) {
            // Show loading state for this specific suggestion
            const suggestionCard = document.querySelectorAll('.suggestion-card')[index];
            const originalContent = suggestionCard.innerHTML;
            
            suggestionCard.innerHTML = `
                <div style="text-align: center; padding: 1rem;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 0.5rem; color: #64748b; font-size: 0.8rem;">Regenerating...</p>
                </div>
            `;
            
            // Simulate regeneration
            setTimeout(() => {
                suggestionCard.innerHTML = originalContent;
                showNotification('New suggestion generated!', 'success');
            }, 2000);
        }

        function saveResume() {
            showNotification('Resume saved successfully!', 'success');
        }

        function downloadResume() {
            showNotification('Preparing download...', 'info');
            setTimeout(() => {
                showNotification('Resume downloaded!', 'success');
            }, 2000);
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
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                font-weight: 500;
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
