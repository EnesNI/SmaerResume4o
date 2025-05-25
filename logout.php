<?php
require_once 'auth.php';

// Log the logout activity
if (isLoggedIn()) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?, 'logout', 'User logged out', ?)");
        $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        // Continue with logout even if logging fails
    }
}

logout();
?>
