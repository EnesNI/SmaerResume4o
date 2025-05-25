<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['pdf_file'];
$filename = $file['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

// Validate file type
if ($ext !== 'pdf') {
    echo json_encode(['success' => false, 'error' => 'Only PDF files are allowed']);
    exit;
}

// Validate file size (10MB max)
if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File size too large. Maximum 10MB allowed']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
        exit;
    }
}

// Generate unique filename
$uniqueFilename = uniqid() . '_' . time() . '_' . $filename;
$filePath = $uploadDir . $uniqueFilename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode([
        'success' => true,
        'filename' => $uniqueFilename,
        'original_name' => $filename,
        'size' => $file['size'],
        'path' => $filePath
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file']);
}
?>
