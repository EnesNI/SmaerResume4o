

<?php

session_start();
$user_name = $_SESSION['user_name'] ?? 'John Doe';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Resume Analyzer - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .analyzer-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .analyzer-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .file-upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: #f1f5f9;
        }
        
        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        
        .upload-text {
            color: #64748b;
            margin-bottom: 1rem;
        }
        
        .upload-text strong {
            color: #1e293b;
        }
        
        .file-input {
            display: none;
        }
        
        .btn-upload {
            background: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-upload:hover {
            background: #5a67d8;
        }
        
        .file-info {
            margin-top: 1rem;
            padding: 1rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            color: #166534;
            display: none;
        }

        .file-actions {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-edit-pdf {
            background: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-edit-pdf:hover {
            background: #059669;
            color: white;
        }

        .btn-analyze {
            background: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-analyze:hover {
            background: #5a67d8;
        }
        
        .form-textarea {
            width: 100%;
            min-height: 150px;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
            resize: vertical;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .analyze-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .analyze-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .analyze-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .tips-card {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .tips-title {
            font-weight: 600;
            color: #0c4a6e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tips-list li {
            color: #0369a1;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .tips-list li::before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-top: 0.1rem;
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
                    <li class="active">
                        <a href="upload-detect.php">
                            <i class="fas fa-search"></i>
                            <span>Analyze Resume</span>
                        </a>
                    </li>
                    <li>
                        <a href="my_resumes.php">
                            <i class="fas fa-folder"></i>
                             <span>My Resume</span>
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
                    <h1>AI Resume Analyzer</h1>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search..." id="searchInput">
                    </div>
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <div class="welcome-text">
                        <h2>AI-Powered Resume Analysis</h2>
                        <p>Get instant feedback on your resume with our advanced AI technology. Upload your resume and job description to receive detailed insights and improvement suggestions.</p>
                    </div>
                    <div class="welcome-actions">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </section>

                <!-- Analyzer Form -->
                <div class="analyzer-container">
                    <div class="analyzer-card">
                        <form action="analyze_resume.php" method="POST" enctype="multipart/form-data" id="analyzeForm">
                            <!-- File Upload Section -->
                            <div class="form-section">
                                <div class="section-title">
                                    <i class="fas fa-file-upload"></i>
                                    Upload Your Resume
                                </div>
                                
                                <div class="file-upload-area" onclick="document.getElementById('resume_file').click()">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">
                                        <strong>Click to upload</strong> or drag and drop your resume
                                        <br>
                                        <small>Supports PDF files (Max 10MB)</small>
                                    </div>
                                    <button type="button" class="btn-upload">
                                        <i class="fas fa-plus"></i>
                                        Choose PDF File
                                    </button>
                                </div>
                                
                                <input type="file" name="resume_file" id="resume_file" class="file-input" accept=".pdf" required>
                                
                                <div class="file-info" id="fileInfo">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="fileName"></span>
                                    <button type="button" onclick="removeFile()" style="float: right; background: none; border: none; color: #dc2626; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <div class="file-actions" id="fileActions" style="display: none;">
                                        <a href="#" class="btn-edit-pdf" id="editPdfBtn">
                                            <i class="fas fa-edit"></i>
                                            Edit PDF with AI
                                        </a>
                                        <button type="button" class="btn-analyze" onclick="proceedToAnalysis()">
                                            <i class="fas fa-search"></i>
                                            Analyze Resume
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field to store uploaded filename -->
                            <input type="hidden" name="uploaded_filename" id="uploadedFilename" value="">

                            <!-- Job Description Section -->
                            <div class="form-section" id="jobDescriptionSection">
                                <div class="section-title">
                                    <i class="fas fa-briefcase"></i>
                                    Job Description
                                </div>
                                
                                <label class="form-label" for="job_description">
                                    Paste the job description you're applying for:
                                </label>
                                <textarea 
                                    name="job_description" 
                                    id="job_description" 
                                    class="form-textarea"
                                    placeholder="Paste the complete job description here. Include requirements, responsibilities, and qualifications for the best analysis results..."
                                    required
                                ></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="analyze-btn" id="submitBtn">
                                <span id="btnText">
                                    <i class="fas fa-brain"></i>
                                    Analyze Resume with AI
                                </span>
                                <span class="loading-spinner" id="loadingSpinner"></span>
                            </button>
                        </form>

                        <!-- Tips Section -->
                        <div class="tips-card">
                            <div class="tips-title">
                                <i class="fas fa-lightbulb"></i>
                                Tips for Better Analysis
                            </div>
                            <ul class="tips-list">
                                <li>Upload a clear, well-formatted PDF resume</li>
                                <li>Use the PDF editor to improve your actual resume content</li>
                                <li>Include the complete job description with all requirements</li>
                                <li>Get AI suggestions for every section of your resume</li>
                                <li>Download your improved resume as a new PDF</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        let uploadedFileName = '';

        // File upload handling
        const fileInput = document.getElementById('resume_file');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const uploadArea = document.querySelector('.file-upload-area');
        const fileActions = document.getElementById('fileActions');
        const editPdfBtn = document.getElementById('editPdfBtn');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type === 'application/pdf') {
                showFileInfo(file);
                uploadFile(file);
            } else {
                alert('Please select a PDF file.');
            }
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'application/pdf') {
                    fileInput.files = files;
                    showFileInfo(file);
                    uploadFile(file);
                } else {
                    alert('Please upload a PDF file.');
                }
            }
        });

        function showFileInfo(file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileName.textContent = `${file.name} (${fileSize} MB)`;
            fileInfo.style.display = 'block';
        }

        async function uploadFile(file) {
            const formData = new FormData();
            formData.append('pdf_file', file);
            
            try {
                showNotification('Uploading PDF...', 'info');
                
                const response = await fetch('upload_resume.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    uploadedFileName = result.filename;
                    document.getElementById('uploadedFilename').value = result.filename;
                    editPdfBtn.href = `interactive-pdf-editor.php?pdf=${encodeURIComponent(result.filename)}`;
                    fileActions.style.display = 'flex';
                    showNotification('PDF uploaded successfully!', 'success');
                } else {
                    showNotification('Upload failed: ' + result.error, 'error');
                    console.error('Upload error:', result);
                }
            } catch (error) {
                console.error('Upload error:', error);
                showNotification('Upload failed. Please try again.', 'error');
            }
        }

        function removeFile() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            fileActions.style.display = 'none';
            uploadedFileName = '';
            document.getElementById('uploadedFilename').value = '';
        }

        function proceedToAnalysis() {
            if (!uploadedFileName) {
                alert('Please upload a PDF file first.');
                return;
            }
            
            const jobDescription = document.getElementById('job_description').value;
            if (!jobDescription.trim()) {
                alert('Please enter a job description.');
                document.getElementById('job_description').focus();
                return;
            }
            
            // Submit the form for analysis
            document.getElementById('analyzeForm').submit();
        }

        // Form submission handling
        document.getElementById('analyzeForm').addEventListener('submit', function(e) {
            if (!uploadedFileName) {
                e.preventDefault();
                alert('Please upload a PDF file first.');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading state
            btnText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            submitBtn.disabled = true;
            submitBtn.style.cursor = 'not-allowed';
        });

        // Character counter for textarea
        const textarea = document.getElementById('job_description');
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            if (length > 0) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#e2e8f0';
            }
        });

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
