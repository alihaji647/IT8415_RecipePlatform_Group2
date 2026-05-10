<?php 
session_start();
require '../config/database.php'; 

if (!isset($_SESSION['user_id']) || !in_array(getUserRole(), ['creator', 'admin'])) {
    header('Location: ../login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if($id > 0) {
    $stmt = $pdo->prepare("UPDATE dbproj_recipes SET status = 'published' WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header('Location: dashboard.php?published=1');
exit;
?>