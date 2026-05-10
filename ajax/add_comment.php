<?php
session_start();
require '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || empty(trim($_POST['comment']))) {
    echo json_encode(['error' => 'Login required or empty comment']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO dbproj_comments (recipe_id, user_id, comment, rating) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        (int)$_POST['recipe_id'], 
        $_SESSION['user_id'], 
        trim($_POST['comment']),
        (int)($_POST['rating'] ?? 0)
    ]);
    echo json_encode(['success' => true, 'message' => 'Comment added!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error']);
}
?>