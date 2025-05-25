<?php
session_start();

$user_name = $_SESSION['user_name'] ?? 'John Doe';
$pdf_file = $_GET['pdf'] ?? '';

if (!$pdf_file || !file_exists('uploads/' . basename($pdf_file))) {
    header('Location: upload-detect.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Resume Editor - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        .editor-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            height: calc(100vh - 80px);
            padding: 2rem;
        }

        .pdf-viewer-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .pdf-toolbar {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: between;
            align-items: center;
            gap: 1rem;
        }

        .pdf-controls {
            display: flex;
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
            border: 2px solid transparent;
            padding: 2px;
            font-family: inherit;
            color: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            pointer-events: auto;
            resize: none;
            overflow: hidden;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .editable-text:hover {
            background: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.3);
            color: #1e293b;
        }

        .editable-text.editing {
            background: rgba(102, 126, 234, 0.15);
            border-color: #667eea;
            color: #1e293b;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            z-index: 10;
        }

        .editable-text.selected {
            background: rgba(16, 185, 129, 0.15);
            border-color: #10b981;
            color: #1e293b;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
            pointer-events: none;
        }

        .editable-text:hover .edit-indicator {
            opacity: 1;
        }

        .ai-assistant {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .assistant-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .assistant-content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .current-selection {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .selection-title {
            font-weight: 600;
            color: #166534;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .selection-text {
            color: #15803d;
            font-size: 0.9rem;
            background: white;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #dcfce7;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 100px;
            overflow-y: auto;
        }

        .ai-suggestions {
            margin-top: 1.5rem;
        }

        .suggestion-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
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
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .suggestion-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .suggestion-text {
            color: #64748b;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .suggested-replacement {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 0.75rem;
            margin: 0.5rem 0;
            font-family: monospace;
            font-size: 0.85rem;
            color: #0369a1;
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

        .loading-suggestions {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .save-actions {
            padding: 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 1rem;
        }

        .btn-save {
            flex: 1;
            background: #10b981;
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background: #059669;
        }

        .btn-download {
            flex: 1;
            background: #667eea;
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background: #5a67d8;
        }

        @media (max-width: 1024px) {
            .editor-container {
                grid-template-columns: 1fr;
                height: auto;
            }
            
            .ai-assistant {
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
                    <li>
                        <a href="resume_form.html">
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
                    <h1>PDF Resume Editor</h1>
                </div>
                <div class="header-right">
                    <a href="upload-detect.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                </div>
            </header>

            <div class="editor-container">
                <!-- PDF Viewer -->
                <div class="pdf-viewer-container">
                    <div class="pdf-toolbar">
                        <div class="pdf-controls">
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
                    </div>
                    
                    <div class="pdf-viewer" id="pdfViewer">
                        <div class="pdf-page-container" id="pageContainer">
                            <canvas id="pdfCanvas" class="pdf-canvas"></canvas>
                            <div class="text-overlay" id="textOverlay"></div>
                        </div>
                    </div>
                </div>

                <!-- AI Assistant -->
                <div class="ai-assistant">
                    <div class="assistant-header">
                        <i class="fas fa-robot"></i>
                        AI Writing Assistant
                    </div>
                    <div class="assistant-content" id="assistantContent">
                        <div class="empty-state">
                            <i class="fas fa-mouse-pointer"></i>
                            <h3>Click on any text</h3>
                            <p>Click on any text in your resume to get AI-powered suggestions for improvement.</p>
                        </div>
                    </div>
                    <div class="save-actions">
                        <button class="btn-save" onclick="saveChanges()">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                        <button class="btn-download" onclick="downloadPDF()">
                            <i class="fas fa-download"></i>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        // PDF.js setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let canvas = document.getElementById('pdfCanvas');
        let ctx = canvas.getContext('2d');
        let editMode = false;
        let textItems = [];
        let selectedTextElement = null;

        // Load PDF
        const pdfPath = 'uploads/<?php echo basename($pdf_file); ?>';
        
        pdfjsLib.getDocument(pdfPath).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            renderPage(pageNum);
        });

        function renderPage(num) {
            pageRendering = true;
            
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
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                    
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
                    const fontHeight = fontSize;
                    
                    const textDiv = document.createElement('textarea');
                    textDiv.className = 'editable-text';
                    textDiv.value = textItem.str;
                    textDiv.style.left = tx[4] + 'px';
                    textDiv.style.top = (viewport.height - tx[5] - fontHeight) + 'px';
                    textDiv.style.fontSize = fontSize + 'px';
                    textDiv.style.width = (textItem.width * scale) + 'px';
                    textDiv.style.height = fontHeight + 'px';
                    textDiv.style.fontFamily = textItem.fontName || 'Arial';
                    
                    // Add edit indicator
                    const editIndicator = document.createElement('div');
                    editIndicator.className = 'edit-indicator';
                    editIndicator.innerHTML = '<i class="fas fa-edit"></i>';
                    textDiv.appendChild(editIndicator);
                    
                    textDiv.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectTextElement(textDiv);
                    });
                    
                    textDiv.addEventListener('input', function() {
                        // Auto-resize textarea
                        this.style.height = 'auto';
                        this.style.height = this.scrollHeight + 'px';
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
                el.classList.remove('selected', 'editing');
            });
            
            // Select current element
            element.classList.add('selected');
            selectedTextElement = element;
            
            // Show AI suggestions for this text
            showAISuggestions(element.value);
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
                    el.style.color = 'rgba(30, 41, 59, 0.7)';
                });
            } else {
                toggleBtn.classList.remove('active');
                toggleBtn.innerHTML = '<i class="fas fa-edit"></i><span>Enable Editing</span>';
                textElements.forEach(el => {
                    el.classList.remove('selected', 'editing');
                    el.style.color = 'transparent';
                });
                selectedTextElement = null;
                showEmptyState();
            }
        }

        function showAISuggestions(text) {
            const assistantContent = document.getElementById('assistantContent');
            
            // Show loading state
            assistantContent.innerHTML = `
                <div class="current-selection">
                    <div class="selection-title">
                        <i class="fas fa-crosshairs"></i>
                        Selected Text
                    </div>
                    <div class="selection-text">${text}</div>
                </div>
                <div class="loading-suggestions">
                    <div class="loading-spinner"></div>
                    <p>AI is analyzing your text...</p>
                </div>
            `;

            // Simulate AI processing
            setTimeout(() => {
                const suggestions = generateAISuggestions(text);
                displaySuggestions(text, suggestions);
            }, 1500);
        }

        function generateAISuggestions(text) {
            // Mock AI suggestions based on text content
            const suggestions = [];
            
            if (text.toLowerCase().includes('responsible for')) {
                suggestions.push({
                    title: 'Use Action Verbs',
                    description: 'Replace "responsible for" with strong action verbs to make your achievements more impactful.',
                    replacement: text.replace(/responsible for/gi, 'Led').replace(/Responsible for/gi, 'Led')
                });
            }
            
            if (text.length < 50 && !text.includes('%') && !text.includes('$')) {
                suggestions.push({
                    title: 'Add Quantifiable Results',
                    description: 'Include specific numbers, percentages, or metrics to demonstrate your impact.',
                    replacement: text + ' (increased efficiency by 25%)'
                });
            }
            
            if (text.toLowerCase().includes('worked on') || text.toLowerCase().includes('helped with')) {
                suggestions.push({
                    title: 'Strengthen Language',
                    description: 'Use more powerful verbs to showcase your contributions.',
                    replacement: text.replace(/worked on/gi, 'developed').replace(/helped with/gi, 'collaborated on')
                });
            }
            
            if (suggestions.length === 0) {
                suggestions.push({
                    title: 'Enhance Clarity',
                    description: 'Consider making this statement more specific and impactful.',
                    replacement: text + ' with measurable results'
                });
            }
            
            return suggestions;
        }

        function displaySuggestions(originalText, suggestions) {
            const assistantContent = document.getElementById('assistantContent');
            
            let suggestionsHtml = `
                <div class="current-selection">
                    <div class="selection-title">
                        <i class="fas fa-crosshairs"></i>
                        Selected Text
                    </div>
                    <div class="selection-text">${originalText}</div>
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
                        <div class="suggestion-text">${suggestion.description}</div>
                        <div class="suggested-replacement">${suggestion.replacement}</div>
                        <div class="suggestion-actions">
                            <button class="btn-apply" onclick="applySuggestion('${suggestion.replacement.replace(/'/g, "\\'")}')">
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
            assistantContent.innerHTML = suggestionsHtml;
        }

        function applySuggestion(newText) {
            if (selectedTextElement) {
                selectedTextElement.value = newText;
                selectedTextElement.style.height = 'auto';
                selectedTextElement.style.height = selectedTextElement.scrollHeight + 'px';
                
                showNotification('Suggestion applied successfully!', 'success');
                
                // Refresh suggestions
                setTimeout(() => {
                    showAISuggestions(newText);
                }, 1000);
            }
        }

        function regenerateSuggestion(index) {
            showNotification('Generating new suggestion...', 'info');
            
            setTimeout(() => {
                showNotification('New suggestion generated!', 'success');
                // In a real implementation, you would call your AI API here
            }, 2000);
        }

        function showEmptyState() {
            const assistantContent = document.getElementById('assistantContent');
            assistantContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-mouse-pointer"></i>
                    <h3>Click on any text</h3>
                    <p>Click on any text in your resume to get AI-powered suggestions for improvement.</p>
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
            const changes = textItems.map(item => ({
                index: item.index,
                originalText: item.originalText,
                newText: item.element.value
            }));
            
            // Here you would send the changes to your backend
            console.log('Saving changes:', changes);
            showNotification('Changes saved successfully!', 'success');
        }

        function downloadPDF() {
            showNotification('Preparing PDF download...', 'info');
            
            // Here you would generate a new PDF with the changes
            setTimeout(() => {
                showNotification('PDF downloaded!', 'success');
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
