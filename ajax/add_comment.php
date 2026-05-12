<?php
// Start session to access user authentication
session_start();
// Include database configuration and PDO connection
require '../config/database.php';
// Set JSON response header
header('Content-Type: application/json');

// Validate: user must be logged in AND comment must not be empty
if (!isset($_SESSION['user_id']) || empty(trim($_POST['comment']))) {
    // Return JSON error for invalid requests
    echo json_encode(['error' => 'Login required or empty comment']);
    exit;
}

try {
    // Prepare INSERT statement for comments table
    $stmt = $pdo->prepare("
        INSERT INTO dbproj_comments (recipe_id, user_id, comment, rating) 
        VALUES (?, ?, ?, ?)
    ");
    // Execute with parameter binding (SQL injection safe)
    $stmt->execute([
        (int)$_POST['recipe_id'],      // Cast recipe_id to integer
        $_SESSION['user_id'],          // Logged-in user ID from session
        trim($_POST['comment']),       // Trim whitespace from comment
        (int)($_POST['rating'] ?? 0)   // Rating with default 0 if not provided
    ]);
    // Success response
    echo json_encode(['success' => true, 'message' => 'Comment added!']);
} catch (Exception $e) {
    // Generic database error response (hides actual error from client)
    echo json_encode(['error' => 'Database error']);
}
?>