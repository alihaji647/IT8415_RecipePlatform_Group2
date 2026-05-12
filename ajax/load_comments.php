<?php
// Include database configuration and PDO connection
require '../config/database.php';
// Sanitize and validate recipe ID from GET parameter
$id = (int)($_GET['recipe_id'] ?? 0);

// Validate recipe ID is positive integer
if($id <= 0) {
    // Early exit for invalid ID
    echo '<p>No comments available</p>';
    exit;
}

// Prepare query to fetch comments with user info
$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM dbproj_comments c 
    JOIN dbproj_users u ON c.user_id = u.id 
    WHERE c.recipe_id = ? 
    ORDER BY c.created_at DESC
    LIMIT 50
");
// Execute with parameter binding (SQL injection safe)
$stmt->execute([$id]);
// Fetch all comments as associative array
$comments = $stmt->fetchAll();

if(empty($comments)) {
    // Empty state message
    echo '<div class="alert alert-secondary">No comments yet. Be the first to comment!</div>';
} else {
    // Loop through each comment
    foreach($comments as $comment): 
?>
        <!-- Individual comment container -->
        <div class="comment-item border-bottom pb-3 mb-3">
            <!-- Flex layout for avatar/username + content -->
            <div class="d-flex align-items-start">
                <!-- Main comment content -->
                <div class="flex-grow-1">
                    <!-- Username (XSS safe) + timestamp -->
                    <strong class="me-2"><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                    <!-- Comment text (XSS safe) -->
                    <p class="mt-2 mb-0"><?php echo htmlspecialchars($comment['comment']); ?></p>
                </div>
                <!-- Rating stars (conditional) -->
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