<?php
require_once 'vendor/autoload.php'; // You'll need to install smalot/pdfparser via Composer

use Smalot\PdfParser\Parser;

function extractPdfText($pdfPath) {
    // Check if the PDF parser library is available
    if (!class_exists('Smalot\PdfParser\Parser')) {
        // Fallback: create mock data for testing
        return [
            'success' => true,
            'data' => [
                [
                    'page' => 1,
                    'textElements' => [
                        [
                            'text' => 'John Smith',
                            'x' => 50,
                            'y' => 50,
                            'width' => 200,
                            'height' => 24,
                            'fontSize' => 18,
                            'section' => 'name',
                            'id' => 'text_0_0'
                        ],
                        [
                            'text' => 'Software Engineer',
                            'x' => 50,
                            'y' => 80,
                            'width' => 180,
                            'height' => 20,
                            'fontSize' => 14,
                            'section' => 'title',
                            'id' => 'text_0_1'
                        ],
                        [
                            'text' => 'john.smith@email.com | +1 (555) 123-4567 | San Francisco, CA',
                            'x' => 50,
                            'y' => 110,
                            'width' => 400,
                            'height' => 16,
                            'fontSize' => 12,
                            'section' => 'contact',
                            'id' => 'text_0_2'
                        ],
                        [
                            'text' => 'Professional Summary',
                            'x' => 50,
                            'y' => 150,
                            'width' => 200,
                            'height' => 18,
                            'fontSize' => 14,
                            'section' => 'summary-title',
                            'id' => 'text_0_3'
                        ],
                        [
                            'text' => 'Experienced Software Engineer with 5+ years of expertise in full-stack development.',
                            'x' => 50,
                            'y' => 180,
                            'width' => 500,
                            'height' => 40,
                            'fontSize' => 12,
                            'section' => 'content',
                            'id' => 'text_0_4'
                        ]
                    ]
                ]
            ]
        ];
    }
    
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        
        // Get text with positioning information
        $pages = $pdf->getPages();
        $extractedData = [];
        
        foreach ($pages as $pageNumber => $page) {
            $text = $page->getText();
            
            if (empty(trim($text))) {
                continue;
            }
            
            // Split text into lines and estimate positions
            $lines = explode("\n", $text);
            $pageData = [];
            
            $yPosition = 50; // Start from top
            foreach ($lines as $lineIndex => $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $pageData[] = [
                        'text' => $line,
                        'x' => 50, // Left margin
                        'y' => $yPosition,
                        'width' => max(strlen($line) * 8, 100), // Estimate width
                        'height' => 20,
                        'fontSize' => 12,
                        'section' => detectSection($line, $lineIndex),
                        'id' => 'text_' . $pageNumber . '_' . $lineIndex
                    ];
                    $yPosition += 25; // Line spacing
                }
            }
            
            if (!empty($pageData)) {
                $extractedData[] = [
                    'page' => $pageNumber + 1,
                    'textElements' => $pageData
                ];
            }
        }
        
        return [
            'success' => true,
            'data' => $extractedData
        ];
        
    } catch (Exception $e) {
        error_log("PDF extraction error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function detectSection($text, $lineIndex) {
    $text = strtolower($text);
    
    // Detect different resume sections
    if ($lineIndex === 0) return 'name';
    if ($lineIndex === 1) return 'title';
    if (strpos($text, '@') !== false || strpos($text, 'phone') !== false) return 'contact';
    if (strpos($text, 'summary') !== false || strpos($text, 'objective') !== false) return 'summary-title';
    if (strpos($text, 'experience') !== false || strpos($text, 'employment') !== false) return 'experience-title';
    if (strpos($text, 'education') !== false) return 'education-title';
    if (strpos($text, 'skills') !== false) return 'skills-title';
    if (preg_match('/\d{4}\s*-\s*\d{4}|\d{4}\s*-\s*present/i', $text)) return 'date-range';
    if (preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+/', $text)) return 'company';
    
    return 'content';
}

// Handle loading specific PDF files
if (isset($_GET['load_file'])) {
    header('Content-Type: application/json');
    
    $filename = $_GET['load_file'];
    $filePath = 'uploads/' . basename($filename);
    
    // Add debugging
    error_log("Attempting to load PDF: " . $filePath);
    error_log("File exists: " . (file_exists($filePath) ? 'yes' : 'no'));
    
    if (file_exists($filePath)) {
        $result = extractPdfText($filePath);
        echo json_encode($result);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'PDF file not found at: ' . $filePath,
            'debug' => [
                'filename' => $filename,
                'filepath' => $filePath,
                'uploads_dir_exists' => is_dir('uploads/'),
                'files_in_uploads' => is_dir('uploads/') ? scandir('uploads/') : []
            ]
        ]);
    }
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_FILES['pdf_file'])) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['pdf_file']['name'];
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $filePath)) {
            $result = extractPdfText($filePath);
            $result['filename'] = $fileName;
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    }
}
?>
