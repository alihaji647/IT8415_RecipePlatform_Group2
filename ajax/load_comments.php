<?php
require '../config/database.php';
$id = (int)($_GET['recipe_id'] ?? 0);

if($id <= 0) {
    echo '<p>No comments available</p>';
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM dbproj_comments c 
    JOIN dbproj_users u ON c.user_id = u.id 
    WHERE c.recipe_id = ? 
    ORDER BY c.created_at DESC
    LIMIT 50
");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

if(empty($comments)) {
    echo '<div class="alert alert-secondary">No comments yet. Be the first to comment!</div>';
} else {
    foreach($comments as $comment): 
?>
        <div class="comment-item border-bottom pb-3 mb-3">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <strong class="me-2"><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                    <p class="mt-2 mb-0"><?php echo htmlspecialchars($comment['comment']); ?></p>
                </div>
                <?php if($comment['rating']): ?>
                <div class="ms-2">
                    <small class="text-warning"><?php echo str_repeat('⭐', $comment['rating']); ?></small>
                </div>
                <?php endif; ?>
            </div>
        </div>
<?php 
    endforeach; 
}
?>