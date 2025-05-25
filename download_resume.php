<?php
session_start();

$template_id = $_GET['template'] ?? 1;
$resume_id = $_GET['resume_id'] ?? null;

// Here you would implement PDF generation
// For now, we'll redirect to the template with print styles

$template_files = [
    1 => 'template_modern.php',
    2 => 'template_creative.php',
    3 => 'template_minimal.php',
    4 => 'template_executive.php'
];

$template_file = $template_files[$template_id] ?? 'template_modern.php';

// Get resume data if resume_id is provided
$resume_data = [];
if ($resume_id) {
    // Database connection and data retrieval logic here
    // For now, redirect to template with print parameter
    header("Location: {$template_file}?print=1&resume_id={$resume_id}");
} else {
    header("Location: {$template_file}?print=1");
}
exit;
?>
