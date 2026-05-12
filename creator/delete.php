<?php 
// Start session for user authentication
session_start();
// Include database connection
require '../config/database.php'; 

// Sanitize recipe ID from GET parameter
$id = (int)($_GET['id'] ?? 0);

// Security: Only delete if valid ID AND user owns the recipe
if($id > 0 && isset($_SESSION['user_id'])) {
    // Prepared statement with dual ownership check (SQL injection safe)
    $stmt = $pdo->prepare("DELETE FROM dbproj_recipes WHERE id = ? AND user_id = ?");
    // Execute with recipe_id + user_id parameters
    $stmt->execute([$id, $_SESSION['user_id']]);
    // Note: No rowCount() check needed - silent fail is secure
}

header('Location: dashboard.php?deleted=1');
exit;
?>