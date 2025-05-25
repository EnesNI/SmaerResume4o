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

$user_id = $_SESSION['user_id'] ?? 1; // Default to user 1 for demo
$template_id = $_POST['template_id'] ?? 1;
$resume_name = $_POST['full_name'] ? $_POST['full_name'] . ' Resume' : 'Untitled Resume';

// Collect form data
$resume_data = [
    'full_name' => $_POST['full_name'] ?? '',
    'title' => $_POST['title'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'location' => $_POST['location'] ?? '',
    'linkedin' => $_POST['linkedin'] ?? '',
    'website' => $_POST['website'] ?? '',
    'summary' => $_POST['summary'] ?? '',
    'experience' => [],
    'education' => [],
    'skills' => []
];

// Process experience data
if (isset($_POST['experience'])) {
    foreach ($_POST['experience'] as $exp) {
        if (!empty($exp['title']) || !empty($exp['company'])) {
            $resume_data['experience'][] = [
                'title' => $exp['title'] ?? '',
                'company' => $exp['company'] ?? '',
                'duration' => $exp['duration'] ?? '',
                'description' => $exp['description'] ?? ''
            ];
        }
    }
}

// Process education data
if (isset($_POST['education'])) {
    foreach ($_POST['education'] as $edu) {
        if (!empty($edu['degree']) || !empty($edu['school'])) {
            $resume_data['education'][] = [
                'degree' => $edu['degree'] ?? '',
                'school' => $edu['school'] ?? '',
                'year' => $edu['year'] ?? ''
            ];
        }
    }
}

// Process skills (from hidden input or form processing)
if (isset($_POST['skills'])) {
    $resume_data['skills'] = array_filter($_POST['skills']);
}

try {
    // Check if resume exists
    $resume_id = $_POST['resume_id'] ?? null;
    
    if ($resume_id) {
        // Update existing resume
        $stmt = $pdo->prepare("UPDATE resumes SET resume_name = ?, template_name = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$resume_name, "Template $template_id", $resume_id, $user_id]);
        
        // Update resume data
        $stmt = $pdo->prepare("UPDATE resume_data SET data = ? WHERE resume_id = ?");
        $stmt->execute([json_encode($resume_data), $resume_id]);
        
    } else {
        // Create new resume
        $stmt = $pdo->prepare("INSERT INTO resumes (user_id, resume_name, template_name, status) VALUES (?, ?, ?, 'draft')");
        $stmt->execute([$user_id, $resume_name, "Template $template_id"]);
        $resume_id = $pdo->lastInsertId();
        
        // Insert resume data
        $stmt = $pdo->prepare("INSERT INTO resume_data (resume_id, data) VALUES (?, ?)");
        $stmt->execute([$resume_id, json_encode($resume_data)]);
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Resume saved successfully',
        'resume_id' => $resume_id
    ]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error saving resume: ' . $e->getMessage()]);
}
?>
