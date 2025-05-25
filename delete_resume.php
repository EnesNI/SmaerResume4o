<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'resume_builder';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$resume_id = $input['resume_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? 1; // Default to user 1 for demo

if (!$resume_id) {
    echo json_encode(['success' => false, 'message' => 'Resume ID is required']);
    exit;
}

try {
    // Check if resume belongs to user
    $stmt = $pdo->prepare("SELECT id FROM resumes WHERE id = ? AND user_id = ?");
    $stmt->execute([$resume_id, $user_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Resume not found or access denied']);
        exit;
    }

    // Delete resume
    $stmt = $pdo->prepare("DELETE FROM resumes WHERE id = ? AND user_id = ?");
    $stmt->execute([$resume_id, $user_id]);

    // Also delete related analytics
    $stmt = $pdo->prepare("DELETE FROM resume_analytics WHERE resume_id = ?");
    $stmt->execute([$resume_id]);

    echo json_encode(['success' => true, 'message' => 'Resume deleted successfully']);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting resume']);
}
?>
