<?php
session_start();
$user_name = $_SESSION['user_name'] ?? 'John Doe';
$pdf_file = $_GET['pdf'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Resume Editor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            height: 100vh;
            overflow: hidden;
        }

        .editor-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            height: 100vh;
        }

        .resume-editor {
            background: white;
            display: flex;
            flex-direction: column;
        }

        .editor-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .editor-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a202c;
        }

        .editor-actions {
            display: flex;
            gap: 1rem;
            margin-left: auto;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #f7fafc;
        }

        .btn-primary {
            background: #f6ad55;
            color: white;
            border-color: #f6ad55;
        }

        .btn-primary:hover {
            background: #ed8936;
        }

        .resume-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        .resume-document {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .resume-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem 2rem;
            position: relative;
        }

        .resume-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .resume-title {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .contact-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .editable-section {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
            cursor: pointer;
            transition: all 0.2s;
        }

        .section-content {
            margin-bottom: 2rem;
        }

        .editable-text {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
            line-height: 1.6;
            color: #4a5568;
        }

        .editable-text:hover {
            background: rgba(102, 126, 234, 0.1);
            outline: 2px solid rgba(102, 126, 234, 0.3);
        }

        .editable-text.selected {
            background: rgba(102, 126, 234, 0.15);
            outline: 2px solid #667eea;
        }

        .experience-item {
            margin-bottom: 2rem;
            padding: 1rem;
            border-left: 3px solid #e2e8f0;
            transition: all 0.2s;
        }

        .experience-item:hover {
            border-left-color: #667eea;
            background: #f7fafc;
        }

        .job-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .company-info {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .job-description {
            color: #4a5568;
            line-height: 1.6;
        }

        .ai-assistant {
            background: white;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .assistant-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .assistant-icon {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .assistant-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .section-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .section-info-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 0.5rem;
        }

        .section-info-text {
            color: #0284c7;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .suggestions-section {
            margin-top: 1.5rem;
        }

        .suggestions-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .suggestions-icon {
            color: #f59e0b;
            font-size: 1.1rem;
        }

        .suggestions-title {
            font-weight: 600;
            color: #1f2937;
        }

        .suggestion-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestion-header {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .suggestion-number {
            background: #10b981;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .suggestion-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .suggestion-description {
            color: #6b7280;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .btn-apply:hover {
            background: #059669;
        }

        .btn-regenerate {
            background: white;
            color: #6b7280;
            border: 1px solid #d1d5db;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-regenerate:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .empty-state h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #6b7280;
        }

        .loading-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .loading-spinner {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .selected-text-preview {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #166534;
            max-height: 100px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <!-- Resume Editor -->
        <div class="resume-editor">
            <div class="editor-header">
                <h1 class="editor-title">Interactive Resume Editor</h1>
                <div class="editor-actions">
                    <button class="btn">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                </div>
            </div>

            <div class="resume-content">
                <div class="resume-document">
                    <!-- Resume Header -->
                    <div class="resume-header">
                        <div class="resume-name editable-text" data-section="name" onclick="selectText(this)">
                            John Smith
                        </div>
                        <div class="resume-title editable-text" data-section="title" onclick="selectText(this)">
                            Software Engineer
                        </div>
                        <div class="contact-info editable-text" data-section="contact" onclick="selectText(this)">
                            john.smith@email.com | +1 (555) 123-4567 | San Francisco, CA | linkedin.com/in/johnsmith
                        </div>
                    </div>

                    <!-- Professional Summary -->
                    <div class="editable-section">
                        <h2 class="section-title editable-text" data-section="summary-title" onclick="selectText(this)">
                            Professional Summary
                        </h2>
                        <div class="section-content">
                            <p class="editable-text" data-section="summary" onclick="selectText(this)">
                                Experienced Software Engineer with 5+ years of expertise in full-stack development.
                            </p>
                        </div>
                    </div>

                    <!-- Professional Experience -->
                    <div class="editable-section">
                        <h2 class="section-title editable-text" data-section="experience-title" onclick="selectText(this)">
                            Professional Experience
                        </h2>
                        <div class="section-content">
                            <div class="experience-item">
                                <div class="job-title editable-text" data-section="job-title" onclick="selectText(this)">
                                    Senior Software Engineer
                                </div>
                                <div class="company-info editable-text" data-section="company" onclick="selectText(this)">
                                    Tech Solutions Inc. | 2022 - Present
                                </div>
                                <div class="job-description editable-text" data-section="job-description" onclick="selectText(this)">
                                    Led development of microservices architecture serving 1M+ users.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Education -->
                    <div class="editable-section">
                        <h2 class="section-title editable-text" data-section="education-title" onclick="selectText(this)">
                            Education
                        </h2>
                        <div class="section-content">
                            <div class="editable-text" data-section="education" onclick="selectText(this)">
                                <strong>Bachelor of Science in Computer Science</strong><br>
                                University of California, Berkeley | 2020
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Assistant -->
        <div class="ai-assistant">
            <div class="assistant-header">
                <div class="assistant-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <span>AI Assistant</span>
            </div>

            <div class="assistant-content" id="assistantContent">
                <div class="section-info">
                    <div class="section-info-header">
                        <i class="fas fa-plus-square"></i>
                        Contact Information
                    </div>
                    <div class="section-info-text">
                        Essential contact information for potential employers.
                    </div>
                </div>

                <div class="empty-state">
                    <i class="fas fa-mouse-pointer"></i>
                    <h3>Click on any text</h3>
                    <p>Click on any text in your resume to get AI-powered suggestions for improvement.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedElement = null;
        let currentSection = null;

        function selectText(element) {
            // Remove previous selection
            document.querySelectorAll('.editable-text').forEach(el => {
                el.classList.remove('selected');
            });

            // Select current element
            element.classList.add('selected');
            selectedElement = element;
            currentSection = element.dataset.section;

            // Show AI suggestions
            showAISuggestions(element.textContent.trim(), currentSection);
        }

        function showAISuggestions(text, section) {
            const assistantContent = document.getElementById('assistantContent');
            
            // Show loading state
            assistantContent.innerHTML = `
                <div class="section-info">
                    <div class="section-info-header">
                        <i class="fas fa-crosshairs"></i>
                        Selected: ${getSectionDisplayName(section)}
                    </div>
                    <div class="section-info-text">
                        Getting AI suggestions for this section...
                    </div>
                </div>
                <div class="selected-text-preview">${text}</div>
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>AI is analyzing your text...</p>
                </div>
            `;

            // Get AI suggestions from Cohere
            getCohereSuggestions(text, section);
        }

        async function getCohereSuggestions(text, section) {
            try {
                const response = await fetch('cohere-suggestions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        text: text,
                        section: section
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    displaySuggestions(text, section, data.suggestions);
                } else {
                    showError('Failed to get AI suggestions: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Error connecting to AI service. Please try again.');
            }
        }

        function displaySuggestions(originalText, section, suggestions) {
            const assistantContent = document.getElementById('assistantContent');
            
            let html = `
                <div class="section-info">
                    <div class="section-info-header">
                        <i class="fas fa-crosshairs"></i>
                        Selected: ${getSectionDisplayName(section)}
                    </div>
                    <div class="section-info-text">
                        AI suggestions for improving this section.
                    </div>
                </div>
                <div class="selected-text-preview">${originalText}</div>
                <div class="suggestions-section">
                    <div class="suggestions-header">
                        <i class="fas fa-lightbulb suggestions-icon"></i>
                        <span class="suggestions-title">AI Suggestions</span>
                    </div>
            `;

            suggestions.forEach((suggestion, index) => {
                html += `
                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-number">${index + 1}</div>
                            <div class="suggestion-title">${suggestion.title}</div>
                        </div>
                        <div class="suggestion-description">${suggestion.description}</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="applySuggestion('${suggestion.improved_text.replace(/'/g, "\\'")}')">
                                <i class="fas fa-check"></i>
                                Apply
                            </button>
                            <button class="btn-regenerate" onclick="regenerateSuggestion('${originalText}', '${section}')">
                                <i class="fas fa-sync"></i>
                                Regenerate
                            </button>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            assistantContent.innerHTML = html;
        }

        function applySuggestion(newText) {
            if (selectedElement) {
                selectedElement.textContent = newText;
                showNotification('Suggestion applied successfully!', 'success');
                
                // Refresh suggestions with new text
                setTimeout(() => {
                    showAISuggestions(newText, currentSection);
                }, 1000);
            }
        }

        function regenerateSuggestion(text, section) {
            showNotification('Generating new suggestions...', 'info');
            getCohereSuggestions(text, section);
        }

        function getSectionDisplayName(section) {
            const sectionNames = {
                'name': 'Name',
                'title': 'Job Title',
                'contact': 'Contact Information',
                'summary': 'Professional Summary',
                'job-title': 'Job Title',
                'company': 'Company Information',
                'job-description': 'Job Description',
                'education': 'Education',
                'summary-title': 'Section Title',
                'experience-title': 'Section Title',
                'education-title': 'Section Title'
            };
            return sectionNames[section] || 'Text Section';
        }

        function showError(message) {
            const assistantContent = document.getElementById('assistantContent');
            assistantContent.innerHTML = `
                <div class="section-info" style="background: #fef2f2; border-color: #fecaca;">
                    <div class="section-info-header" style="color: #dc2626;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error
                    </div>
                    <div class="section-info-text" style="color: #dc2626;">
                        ${message}
                    </div>
                </div>
            `;
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
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
            notification.textContent = message;

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
