<?php
require '../config/database.php';
require '../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = 'Invalid content ID';
    header('Location: dashboard.php');
    exit;
}

requireAdmin();

$content_id = (int)$_GET['id'];

// Delete content (cascade will handle related records)
$stmt = $pdo->prepare("DELETE FROM dbproj_content WHERE content_id = ?");
$stmt->execute([$content_id]);

$_SESSION['message'] = 'Content deleted successfully!';
header('Location: dashboard.php');
exit;
?>

