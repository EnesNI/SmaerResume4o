<?php
session_start();
$user_name = $_SESSION['user_name'] ?? 'John Doe';

// Get PDF file from URL parameter
$pdf_file = $_GET['pdf'] ?? '';
$from_analysis = $_GET['from_analysis'] ?? false;
$match_score = $_GET['match_score'] ?? '';
$ai_score = $_GET['ai_score'] ?? '';
$missing_keywords = $_GET['missing_keywords'] ?? '';

$pdf_path = '';
$pdf_exists = false;

if (!empty($pdf_file)) {
    $pdf_path = 'uploads/' . basename($pdf_file);
    $pdf_exists = file_exists($pdf_path);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Resume Editor - SmartResume</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
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

        .editor-header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .editor-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a202c;
        }

        .editor-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        .btn-save {
            background: #f6ad55;
            color: white;
        }

        .btn-save:hover {
            background: #ed8936;
        }

        .btn-download {
            background: #f6ad55;
            color: white;
        }

        .btn-download:hover {
            background: #ed8936;
        }

        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-ai:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        .editor-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            height: calc(100vh - 80px);
        }

        .pdf-viewer-container {
            background: white;
            display: flex;
            flex-direction: column;
        }

        .pdf-toolbar {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 1rem;
        }

        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .zoom-btn {
            background: #667eea;
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .zoom-btn:hover {
            background: #5a67d8;
        }

        .zoom-level {
            font-size: 0.9rem;
            color: #64748b;
            min-width: 60px;
            text-align: center;
        }

        .edit-mode-toggle {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .edit-mode-toggle:hover {
            background: #059669;
        }

        .edit-mode-toggle.active {
            background: #dc2626;
        }

        .edit-mode-toggle.active:hover {
            background: #b91c1c;
        }

        .pdf-viewer {
            flex: 1;
            overflow: auto;
            position: relative;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 2rem;
        }

        .pdf-page-container {
            position: relative;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }

        .pdf-canvas {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .text-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .editable-text {
            position: absolute;
            background: transparent;
            border: none;
            padding: 2px;
            font-family: inherit;
            color: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            pointer-events: auto;
            resize: none;
            overflow: hidden;
            white-space: pre-wrap;
            word-wrap: break-word;
            outline: none;
        }

        .editable-text:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #1e293b;
        }

        .editable-text.editing {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #667eea;
            color: #1e293b;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            z-index: 10;
            border-radius: 3px;
        }

        .editable-text.selected {
            background: rgba(16, 185, 129, 0.1);
            color: #1e293b;
            border: 2px solid #10b981;
            border-radius: 3px;
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

        .assistant-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .analysis-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .analysis-info-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 0.5rem;
        }

        .analysis-info-text {
            color: #0284c7;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .how-to-edit {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .how-to-edit-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #166534;
            margin-bottom: 0.5rem;
        }

        .how-to-edit-text {
            color: #166534;
            font-size: 0.9rem;
            line-height: 1.5;
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
            color: #64748b;
        }

        .loading-spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        .suggestion-text {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            color: #0369a1;
            line-height: 1.4;
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
    </style>
</head>
<body>
    <div class="editor-header">
        <h1 class="editor-title">Interactive Resume Editor</h1>
        <div class="editor-actions">
            <button class="btn btn-back" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
                Back
            </button>
            <button class="btn btn-save" onclick="saveChanges()">
                <i class="fas fa-save"></i>
                Save
            </button>
            <button class="btn btn-download" onclick="downloadPDF()">
                <i class="fas fa-download"></i>
                Download
            </button>
            <button class="btn btn-ai">
                <i class="fas fa-robot"></i>
                AI Assistant
            </button>
        </div>
    </div>

    <div class="editor-container">
        <!-- PDF Viewer -->
        <div class="pdf-viewer-container">
            <div class="pdf-toolbar">
                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="zoomOut()">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="zoom-level" id="zoomLevel">100%</span>
                    <button class="zoom-btn" onclick="zoomIn()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <button class="edit-mode-toggle" id="editModeToggle" onclick="toggleEditMode()">
                    <i class="fas fa-edit"></i>
                    <span>Enable Editing</span>
                </button>
            </div>
            
            <div class="pdf-viewer" id="pdfViewer">
                <?php if ($pdf_exists): ?>
                <div class="loading-state" id="loadingState">
                    <div class="loading-spinner"></div>
                    <p>Loading PDF...</p>
                </div>
                <div class="pdf-page-container" id="pageContainer" style="display: none;">
                    <canvas id="pdfCanvas" class="pdf-canvas"></canvas>
                    <div class="text-overlay" id="textOverlay"></div>
                </div>
                <?php else: ?>
                <div class="upload-area">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3 class="upload-title">Upload your PDF resume</h3>
                    <p class="upload-subtitle">Upload your PDF resume to start editing with AI-powered suggestions.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- AI Assistant -->
        <div class="ai-assistant">
            <div class="assistant-header">
                <i class="fas fa-robot"></i>
                AI Assistant
            </div>
            <div class="assistant-content" id="assistantContent">
                <?php if ($from_analysis): ?>
                <div class="analysis-info">
                    <div class="analysis-info-header">
                        <i class="fas fa-chart-line"></i>
                        Analysis Results
                    </div>
                    <div class="analysis-info-text">
                        <?php if ($match_score): ?>
                        <strong>Match Score:</strong> <?php echo htmlspecialchars($match_score); ?><?php echo is_numeric($match_score) ? '%' : ''; ?><br>
                        <?php endif; ?>
                        <?php if ($ai_score): ?>
                        <strong>AI Detection:</strong> <?php echo htmlspecialchars($ai_score); ?>%<br>
                        <?php endif; ?>
                        <?php if ($missing_keywords): ?>
                        <strong>Missing Keywords:</strong> <?php echo htmlspecialchars(str_replace(',', ', ', $missing_keywords)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="how-to-edit">
                    <div class="how-to-edit-header">
                        <i class="fas fa-info-circle"></i>
                        How to Edit
                    </div>
                    <div class="how-to-edit-text">
                        • Click on any text to start editing<br>
                        • Type to modify the content<br>
                        • AI suggestions appear automatically<br>
                        • Press Enter to finish editing
                    </div>
                </div>
                
                <div class="empty-state">
                    <i class="fas fa-edit"></i>
                    <h3>Start Editing</h3>
                    <p>Click on any text in your resume to start editing and get AI-powered suggestions.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PDF.js setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let scale = 1.0;
        let canvas = document.getElementById('pdfCanvas');
        let ctx = canvas ? canvas.getContext('2d') : null;
        let editMode = false;
        let textItems = [];
        let selectedTextElement = null;

        // Check if we have a PDF to load
        const pdfExists = <?php echo $pdf_exists ? 'true' : 'false'; ?>;
        const pdfPath = '<?php echo $pdf_path; ?>';

        if (pdfExists && pdfPath) {
            loadPDF(pdfPath);
        }

        function loadPDF(path) {
            pdfjsLib.getDocument(path).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                renderPage(pageNum);
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                showError('Failed to load PDF: ' + error.message);
            });
        }

        function renderPage(num) {
            if (!pdfDoc) return;
            
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('pageContainer').style.display = 'block';
                    
                    // Extract text with positions
                    extractTextWithPositions(page, viewport);
                });
            });
        }

        function extractTextWithPositions(page, viewport) {
            page.getTextContent().then(function(textContent) {
                const textOverlay = document.getElementById('textOverlay');
                textOverlay.innerHTML = '';
                textItems = [];

                textContent.items.forEach(function(textItem, index) {
                    const tx = pdfjsLib.Util.transform(viewport.transform, textItem.transform);
                    const fontSize = Math.sqrt(tx[2] * tx[2] + tx[3] * tx[3]);
                    const fontHeight = fontSize * 1.2;
                    
                    const textDiv = document.createElement('textarea');
                    textDiv.className = 'editable-text';
                    textDiv.value = textItem.str;
                    textDiv.style.left = tx[4] + 'px';
                    textDiv.style.top = (viewport.height - tx[5] - fontHeight) + 'px';
                    textDiv.style.fontSize = fontSize + 'px';
                    textDiv.style.width = Math.max(textItem.width * scale, 50) + 'px';
                    textDiv.style.height = fontHeight + 'px';
                    textDiv.style.fontFamily = textItem.fontName || 'Arial, sans-serif';
                    textDiv.style.lineHeight = '1.2';
                    textDiv.style.pointerEvents = editMode ? 'auto' : 'none';
                    textDiv.readOnly = !editMode;
                    
                    // Click handler
                    textDiv.addEventListener('click', function(e) {
                        if (!editMode) return;
                        e.stopPropagation();
                        selectTextElement(this);
                    });
                    
                    // Focus handler
                    textDiv.addEventListener('focus', function() {
                        if (!editMode) return;
                        this.classList.add('editing');
                        this.style.color = '#1e293b';
                    });
                    
                    // Blur handler
                    textDiv.addEventListener('blur', function() {
                        this.classList.remove('editing');
                        if (!this.classList.contains('selected')) {
                            this.style.color = 'transparent';
                        }
                    });
                    
                    // Input handler
                    textDiv.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = Math.max(this.scrollHeight, fontHeight) + 'px';
                    });
                    
                    // Enter key handler
                    textDiv.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            this.blur();
                        }
                    });
                    
                    textOverlay.appendChild(textDiv);
                    textItems.push({
                        element: textDiv,
                        originalText: textItem.str,
                        index: index
                    });
                });
            });
        }

        function selectTextElement(element) {
            // Remove previous selection
            document.querySelectorAll('.editable-text').forEach(el => {
                el.classList.remove('selected');
                if (!el.classList.contains('editing')) {
                    el.style.color = 'transparent';
                }
            });
            
            // Select current element
            element.classList.add('selected');
            element.style.color = '#1e293b';
            selectedTextElement = element;
            
            // Get AI suggestions
            const text = element.value.trim();
            if (text.length > 0) {
                getAISuggestions(text, detectTextSection(text));
            }
        }

        function detectTextSection(text) {
            const lowerText = text.toLowerCase();
            
            if (lowerText.includes('@') || lowerText.includes('phone') || lowerText.includes('email')) {
                return 'contact';
            } else if (lowerText.includes('summary') || lowerText.includes('objective')) {
                return 'summary';
            } else if (lowerText.includes('experience') || lowerText.includes('work')) {
                return 'experience';
            } else if (lowerText.includes('education') || lowerText.includes('degree')) {
                return 'education';
            } else if (lowerText.includes('skills') || lowerText.includes('technologies')) {
                return 'skills';
            } else if (text.length > 50) {
                return 'job-description';
            } else {
                return 'general';
            }
        }

        async function getAISuggestions(text, section) {
            const assistantContent = document.getElementById('assistantContent');
            
            // Show loading
            assistantContent.innerHTML = `
                <div class="analysis-info">
                    <div class="analysis-info-header">
                        <i class="fas fa-crosshairs"></i>
                        Selected: ${getSectionDisplayName(section)}
                    </div>
                    <div class="analysis-info-text">
                        "${text.substring(0, 100)}${text.length > 100 ? '...' : ''}"
                    </div>
                </div>
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>AI is analyzing your text...</p>
                </div>
            `;

            // Simulate AI processing with mock suggestions
            setTimeout(() => {
                const suggestions = generateMockSuggestions(text, section);
                displaySuggestions(text, section, suggestions);
            }, 1500);
        }

        function generateMockSuggestions(text, section) {
            const suggestions = [];
            
            switch (section) {
                case 'summary':
                    suggestions.push({
                        title: 'Add Quantifiable Achievements',
                        description: 'Include specific numbers and metrics to demonstrate your impact.',
                        improved_text: text + ' with 5+ years of experience delivering 15+ successful projects and improving efficiency by 40%.'
                    });
                    suggestions.push({
                        title: 'Highlight Key Technologies',
                        description: 'Mention your core technical skills early to catch recruiters\' attention.',
                        improved_text: text.replace(/\.$/, '') + ', specializing in React, Node.js, and cloud technologies.'
                    });
                    break;
                    
                case 'job-description':
                    suggestions.push({
                        title: 'Use Strong Action Verbs',
                        description: 'Start with powerful action verbs to make achievements more impactful.',
                        improved_text: text.replace(/managed/gi, 'led').replace(/worked on/gi, 'architected').replace(/helped/gi, 'spearheaded')
                    });
                    suggestions.push({
                        title: 'Add Business Impact',
                        description: 'Connect technical work to business outcomes.',
                        improved_text: text + ' This resulted in 25% increased efficiency and $200K cost savings.'
                    });
                    break;
                    
                case 'skills':
                    suggestions.push({
                        title: 'Add Trending Technologies',
                        description: 'Include current in-demand technologies for your field.',
                        improved_text: text + ', TypeScript, GraphQL, Docker, CI/CD'
                    });
                    break;
                    
                default:
                    suggestions.push({
                        title: 'Enhance Clarity',
                        description: 'Make this section more specific and impactful.',
                        improved_text: text.charAt(0).toUpperCase() + text.slice(1) + ' with proven results.'
                    });
            }
            
            return suggestions;
        }

        function displaySuggestions(originalText, section, suggestions) {
            const assistantContent = document.getElementById('assistantContent');
            
            let html = `
                <div class="analysis-info">
                    <div class="analysis-info-header">
                        <i class="fas fa-crosshairs"></i>
                        Selected: ${getSectionDisplayName(section)}
                    </div>
                    <div class="analysis-info-text">
                        "${originalText.substring(0, 100)}${originalText.length > 100 ? '...' : ''}"
                    </div>
                </div>
                <h4 style="color: #1e293b; margin-bottom: 1rem;">
                    <i class="fas fa-lightbulb" style="color: #fbbf24; margin-right: 0.5rem;"></i>
                    AI Suggestions
                </h4>
            `;

            suggestions.forEach((suggestion, index) => {
                html += `
                    <div class="suggestion-card">
                        <div class="suggestion-header">
                            <div class="suggestion-number">${index + 1}</div>
                            <div class="suggestion-title">${suggestion.title}</div>
                        </div>
                        <div class="suggestion-description">${suggestion.description}</div>
                        <div class="suggestion-text">${suggestion.improved_text}</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="applySuggestion('${suggestion.improved_text.replace(/'/g, "\\'")}')">
                                <i class="fas fa-check"></i> Apply
                            </button>
                            <button class="btn-regenerate" onclick="regenerateSuggestion('${originalText.replace(/'/g, "\\'")}', '${section}')">
                                <i class="fas fa-sync"></i> Regenerate
                            </button>
                        </div>
                    </div>
                `;
            });

            assistantContent.innerHTML = html;
        }

        function applySuggestion(newText) {
            if (selectedTextElement) {
                selectedTextElement.value = newText;
                selectedTextElement.style.height = 'auto';
                selectedTextElement.style.height = Math.max(selectedTextElement.scrollHeight, 20) + 'px';
                showNotification('Suggestion applied successfully!', 'success');
            }
        }

        function regenerateSuggestion(text, section) {
            showNotification('Generating new suggestions...', 'info');
            getAISuggestions(text, section);
        }

        function getSectionDisplayName(section) {
            const sectionNames = {
                'contact': 'Contact Information',
                'summary': 'Professional Summary',
                'experience': 'Experience Section',
                'education': 'Education',
                'skills': 'Skills',
                'job-description': 'Job Description',
                'general': 'Text Content'
            };
            return sectionNames[section] || 'Text Section';
        }

        function toggleEditMode() {
            editMode = !editMode;
            const toggleBtn = document.getElementById('editModeToggle');
            const textElements = document.querySelectorAll('.editable-text');
            
            if (editMode) {
                toggleBtn.classList.add('active');
                toggleBtn.innerHTML = '<i class="fas fa-times"></i><span>Exit Editing</span>';
                textElements.forEach(el => {
                    el.style.pointerEvents = 'auto';
                    el.readOnly = false;
                });
                showNotification('Edit mode enabled! Click on any text to edit.', 'info');
            } else {
                toggleBtn.classList.remove('active');
                toggleBtn.innerHTML = '<i class="fas fa-edit"></i><span>Enable Editing</span>';
                textElements.forEach(el => {
                    el.classList.remove('selected', 'editing');
                    el.style.color = 'transparent';
                    el.style.pointerEvents = 'none';
                    el.readOnly = true;
                });
                selectedTextElement = null;
                showInitialState();
                showNotification('Edit mode disabled.', 'info');
            }
        }

        function showInitialState() {
            const assistantContent = document.getElementById('assistantContent');
            assistantContent.innerHTML = `
                <?php if ($from_analysis): ?>
                <div class="analysis-info">
                    <div class="analysis-info-header">
                        <i class="fas fa-chart-line"></i>
                        Analysis Results
                    </div>
                    <div class="analysis-info-text">
                        <?php if ($match_score): ?>
                        <strong>Match Score:</strong> <?php echo htmlspecialchars($match_score); ?><?php echo is_numeric($match_score) ? '%' : ''; ?><br>
                        <?php endif; ?>
                        <?php if ($ai_score): ?>
                        <strong>AI Detection:</strong> <?php echo htmlspecialchars($ai_score); ?>%<br>
                        <?php endif; ?>
                        <?php if ($missing_keywords): ?>
                        <strong>Missing Keywords:</strong> <?php echo htmlspecialchars(str_replace(',', ', ', $missing_keywords)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="how-to-edit">
                    <div class="how-to-edit-header">
                        <i class="fas fa-info-circle"></i>
                        How to Edit
                    </div>
                    <div class="how-to-edit-text">
                        • Click on any text to start editing<br>
                        • Type to modify the content<br>
                        • AI suggestions appear automatically<br>
                        • Press Enter to finish editing
                    </div>
                </div>
                
                <div class="empty-state">
                    <i class="fas fa-edit"></i>
                    <h3>Start Editing</h3>
                    <p>Click on any text in your resume to start editing and get AI-powered suggestions.</p>
                </div>
            `;
        }

        function zoomIn() {
            scale += 0.1;
            updateZoom();
        }

        function zoomOut() {
            if (scale > 0.3) {
                scale -= 0.1;
                updateZoom();
            }
        }

        function updateZoom() {
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
            renderPage(pageNum);
        }

        function saveChanges() {
            showNotification('Changes saved successfully!', 'success');
        }

        function downloadPDF() {
            showNotification('Preparing PDF download...', 'info');
        }

        function goBack() {
            window.history.back();
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

        function showError(message) {
            document.getElementById('loadingState').innerHTML = `
                <div class="error-state">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Error Loading PDF</h3>
                    <p>${message}</p>
                </div>
            `;
        }

        // Click outside to deselect
        document.addEventListener('click', function(e) {
            if (editMode && !e.target.classList.contains('editable-text')) {
                if (selectedTextElement) {
                    selectedTextElement.classList.remove('selected');
                    selectedTextElement.style.color = 'transparent';
                    selectedTextElement = null;
                    showInitialState();
                }
            }
        });
    </script>
</body>
</html>
