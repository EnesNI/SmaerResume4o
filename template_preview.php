<?php
// Get template ID and data
$template_id = $_GET['template'] ?? 1;
$data_json = $_GET['data'] ?? '';

// Decode data if provided
$template_data = [];
if ($data_json) {
    $template_data = json_decode(urldecode($data_json), true) ?: [];
}

// Map template IDs to files
$template_files = [
    1 => 'template_modern.php',
    2 => 'template_creative.php',
    3 => 'template_minimal.php',
    4 => 'template_executive.php'
];

$template_file = $template_files[$template_id] ?? 'template_modern.php';

// Set the data for the template
$_GET['data'] = $template_data;

// Include the template file
if (file_exists($template_file)) {
    include $template_file;
} else {
    echo "Template not found.";
}
?>
