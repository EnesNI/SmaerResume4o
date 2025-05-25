<?php
session_start();
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobDescription = $_POST['job_description'] ?? '';
    if (!$jobDescription) {
        die("Job description is required.");
    }

    // Get the uploaded filename from the hidden field
    $uploadedFilename = $_POST['uploaded_filename'] ?? '';
    
    if (empty($uploadedFilename)) {
        die("No file was uploaded. Please upload a PDF file first.");
    }

    // Construct the file path
    $uploadPath = 'uploads/' . basename($uploadedFilename);
    
    if (!file_exists($uploadPath)) {
        die("Uploaded file not found. Please upload the file again.");
    }

    // Extract text from the uploaded file
    $ext = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));
    $resumeText = extractTextFromResume($uploadPath, $ext);

    if (empty(trim($resumeText))) {
        die("Failed to extract text from resume.");
    }

    // Your Cohere API key
    $cohereApiKey = 'fHtGUtc0nPqPdizte9rFayjFCQxZGqjrhkE4NQdh';

    // Calculate similarity score using embeddings
    $matchScore = getSimilarityScore($resumeText, $jobDescription, $cohereApiKey);
    if ($matchScore === null) {
        $matchScore = "Unavailable";
    }

    // Simple AI content detection
    $aiScore = detectAIContent($resumeText);

    // Get detailed AI-powered resume analysis using Cohere generation
    $aiAnalysis = analyzeWithAI($resumeText, $jobDescription);

    // Get specific editing suggestions
    $editSuggestions = getEditingSuggestions($resumeText, $jobDescription, $matchScore, $aiScore);

    // Calculate missing keywords
    $resumeWords = array_count_values(str_word_count(strtolower($resumeText), 1));
    $jobWords = array_unique(str_word_count(strtolower($jobDescription), 1));
    
    $missing = [];
    foreach ($jobWords as $word) {
        if (!isset($resumeWords[$word])) {
            $missing[] = $word;
        }
    }

    $user_name = $_SESSION['user_name'] ?? 'John Doe';
} else {
    die("Please submit the form.");
}

// Helper function to get score class
function getScoreClass($score) {
    if (is_numeric($score)) {
        if ($score >= 70) return 'positive';
        if ($score >= 40) return 'neutral';
        return 'negative';
    }
    return 'neutral';
}

// Helper function to get score icon
function getScoreIcon($score) {
    if (is_numeric($score)) {
        if ($score >= 70) return 'green';
        if ($score >= 40) return 'orange';
        return 'purple';
    }
    return 'blue';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Analysis Result - SmartResume</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="detector.css">
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
                    <h1>Resume Analysis Result</h1>
                </div>
                <div class="header-right">
                    <a href="upload-detect.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Go Back
                    </a>
                    <a href="interactive-pdf-editor.php?pdf=<?php echo urlencode($uploadedFilename); ?>&from_analysis=1&match_score=<?php echo urlencode($matchScore); ?>&ai_score=<?php echo urlencode($aiScore); ?>&missing_keywords=<?php echo urlencode(implode(',', array_slice($missing, 0, 10))); ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Resume
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="container">
                    <!-- Results Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon <?php echo getScoreIcon($matchScore); ?>">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo htmlspecialchars($matchScore); ?><?php echo is_numeric($matchScore) ? '%' : ''; ?></h3>
                                <p>Job Match Score</p>
                                <span class="stat-change <?php echo getScoreClass($matchScore); ?>">
                                    <?php echo is_numeric($matchScore) && $matchScore >= 70 ? 'Excellent match' : (is_numeric($matchScore) && $matchScore >= 40 ? 'Good match' : 'Needs improvement'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon <?php echo getScoreIcon($aiScore); ?>">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo htmlspecialchars($aiScore); ?>%</h3>
                                <p>AI-Writing Probability</p>
                                <span class="stat-change <?php echo getScoreClass(100 - $aiScore); ?>">
                                    <?php echo $aiScore < 30 ? 'Looks human' : ($aiScore < 60 ? 'Possibly AI' : 'Likely AI-generated'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo count($missing); ?></h3>
                                <p>Missing Keywords</p>
                                <span class="stat-change <?php echo count($missing) < 5 ? 'positive' : (count($missing) < 15 ? 'neutral' : 'negative'); ?>">
                                    <?php echo count($missing) < 5 ? 'Great coverage' : (count($missing) < 15 ? 'Room for improvement' : 'Add more keywords'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Suggestions (Hidden by default) -->
                    <div id="editSuggestions" class="edit-suggestions hidden">
                        <h3><i class="fas fa-lightbulb"></i> AI Editing Suggestions</h3>
                        <p style="margin-bottom: 1.5rem; color: #64748b;">Based on the analysis, here are specific recommendations to improve your resume:</p>
                        
                        <?php foreach ($editSuggestions as $index => $suggestion): ?>
                        <div class="suggestion-item">
                            <div class="suggestion-icon"><?php echo $index + 1; ?></div>
                            <div class="suggestion-content">
                                <div class="suggestion-title"><?php echo htmlspecialchars($suggestion['title']); ?></div>
                                <div class="suggestion-description"><?php echo htmlspecialchars($suggestion['description']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Missing Keywords Section -->
                    <?php if (!empty($missing)): ?>
                    <div class="keywords-card">
                        <h3><i class="fas fa-tags"></i> Missing Keywords</h3>
                        <div class="keywords-list">
                            <strong>Consider adding these keywords to improve your match score:</strong><br>
                            <?php echo htmlspecialchars(implode(', ', array_slice($missing, 0, 15))); ?>
                            <?php if (count($missing) > 15): ?>
                                <br><em>...and <?php echo count($missing) - 15; ?> more</em>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- AI Analysis Section -->
                    <div class="analysis-card">
                        <h3><i class="fas fa-brain"></i> AI-Powered Resume Insights</h3>
                        <pre><?php echo htmlspecialchars($aiAnalysis); ?></pre>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
    <script>
        function toggleEditSuggestions() {
            const suggestions = document.getElementById('editSuggestions');
            const button = document.querySelector('.btn-primary');
            
            if (suggestions.classList.contains('hidden')) {
                suggestions.classList.remove('hidden');
                button.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Suggestions';
                suggestions.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                suggestions.classList.add('hidden');
                button.innerHTML = '<i class="fas fa-edit"></i> Edit Resume';
            }
        }

        // Auto-scroll to suggestions if match score is low
        document.addEventListener('DOMContentLoaded', function() {
            const matchScore = <?php echo is_numeric($matchScore) ? $matchScore : 0; ?>;
            if (matchScore < 50) {
                // Auto-show suggestions for low scores
                setTimeout(() => {
                    const button = document.querySelector('.btn-primary');
                    if (button) {
                        button.style.animation = 'pulse 2s infinite';
                        button.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.5)';
                    }
                }, 2000);
            }
        });

        // Pulse animation for low scores
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);

        function openInteractiveEditor() {
            const analysisData = {
                matchScore: <?php echo is_numeric($matchScore) ? $matchScore : 0; ?>,
                aiScore: <?php echo $aiScore; ?>,
                missingKeywords: <?php echo json_encode(array_slice($missing, 0, 10)); ?>,
                suggestions: <?php echo json_encode($editSuggestions); ?>
            };
            
            // Store analysis data in sessionStorage for the editor
            sessionStorage.setItem('resumeAnalysisData', JSON.stringify(analysisData));
            
            // Open the interactive editor
            window.open('interactive-editor.php?from_analysis=1', '_blank');
        }
    </script>
</body>
</html>
