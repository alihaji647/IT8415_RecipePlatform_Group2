<?php
// Include database configuration file
require '../config/database.php';
// Include custom functions (likely contains requireAdmin(), isLoggedIn(), etc.)
require '../includes/functions.php';

// Validate GET parameter exists and is numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Set error message in session and redirect to dashboard
    $_SESSION['message'] = 'Invalid content ID';
    header('Location: dashboard.php');
    exit;
}

// Require admin privileges (function from functions.php)
requireAdmin();

$content_id = (int)$_GET['id']; // Cast to integer for safety (SQL injection prevention)

// Delete content (cascade will handle related records)
$stmt = $pdo->prepare("DELETE FROM dbproj_content WHERE content_id = ?");
// Execute prepared statement with parameter binding
$stmt->execute([$content_id]);

// Set success message in session
$_SESSION['message'] = 'Content deleted successfully!';
// Redirect back to dashboard
header('Location: dashboard.php');
exit;
?>