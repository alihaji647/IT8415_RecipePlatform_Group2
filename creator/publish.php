<?php 
// Start session for authentication
session_start();
// Include database connection
require '../config/database.php'; 

// Restrict to creators and admins only
if (!isset($_SESSION['user_id']) || !in_array(getUserRole(), ['creator', 'admin'])) {
    // Redirect unauthorized users to login
    header('Location: ../login.php');
    exit;
}

// Sanitize recipe ID from GET parameter
$id = (int)($_GET['id'] ?? 0);

// Publish recipe (only if user owns it)
if($id > 0) {
    // UPDATE with ownership verification (SQL injection safe)
    $stmt = $pdo->prepare("UPDATE dbproj_recipes SET status = 'published' WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    // Note: No rowCount() check - silent fail is secure (can't publish others' recipes)
}

// Redirect to dashboard with success flag
header('Location: dashboard.php?published=1');
exit;
?>