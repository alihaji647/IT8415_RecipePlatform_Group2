<?php 
session_start();
require '../config/database.php'; 

$id = (int)($_GET['id'] ?? 0);
if($id > 0 && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM dbproj_recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header('Location: dashboard.php?deleted=1');
exit;
?>