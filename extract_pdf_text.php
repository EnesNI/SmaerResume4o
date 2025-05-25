<?php
header('Content-Type: application/json');

require_once 'autoload.php';
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['pdf_file'];
$filename = $file['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if ($ext !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Only PDF files are supported']);
    exit;
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

$uploadPath = 'uploads/' . uniqid() . '_' . basename($filename);

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    exit;
}

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($uploadPath);
    $text = $pdf->getText();
    
    // Clean up the uploaded file
    unlink($uploadPath);
    
    if (empty(trim($text))) {
        echo json_encode(['success' => false, 'message' => 'No text could be extracted from the PDF']);
        exit;
    }
    
    // Clean up the extracted text
    $text = preg_replace('/\s+/', ' ', $text); // Replace multiple spaces with single space
    $text = trim($text);
    
    echo json_encode([
        'success' => true, 
        'text' => $text,
        'length' => strlen($text),
        'word_count' => str_word_count($text)
    ]);
    
} catch (Exception $e) {
    // Clean up the uploaded file in case of error
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error parsing PDF: ' . $e->getMessage()
    ]);
}
?>
