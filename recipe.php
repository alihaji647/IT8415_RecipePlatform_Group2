<?php 
// Start session and include database connection
session_start();
require 'config/database.php'; 

// Get recipe ID from URL parameter and cast to integer for security
$id = (int)($_GET['id'] ?? 0);

// Validate recipe ID
if($id <= 0) {
    die('<div class="alert alert-danger">Invalid recipe ID</div>');
}

// Fetch recipe details with category and author info (published only)
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category, u.username as author 
    FROM dbproj_recipes r 
    JOIN dbproj_categories c ON r.category_id = c.id
    JOIN dbproj_users u ON r.user_id = u.id
    WHERE r.id = ? AND r.status = 'published'
");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

// Check if recipe exists and is published
if (!$recipe) {
    die('<div class="alert alert-danger">Recipe not found or not published</div>');
}

// Increment recipe view count
$pdo->prepare("UPDATE dbproj_recipes SET views = views + 1 WHERE id = ?")->execute([$id]);

// Get total comment count for this recipe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM dbproj_comments WHERE recipe_id = ?");
$stmt->execute([$id]);
$commentCount = $stmt->fetchColumn();

// Set page title to recipe title (XSS safe)
$page_title = htmlspecialchars($recipe['title']);
?>
<!-- Include common header -->
<?php include 'includes/header.php'; ?>

<!-- Main container -->
<div class="container mt-4">
    <div class="row">
        <!-- Main content column (8/12 width on large screens) -->
        <div class="col-lg-8">
            <!-- Recipe Image - Display if image exists -->
            <?php if($recipe['image_path']): ?>
            <img src="uploads/images/<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                 class="img-fluid rounded mb-4 shadow" style="height: 350px; object-fit: cover;">
            <?php endif; ?>
            
            <!-- Recipe title -->
            <h1 class="mb-3"><?php echo htmlspecialchars($recipe['title']); ?></h1>
            <!-- Recipe metadata (author, category, views) -->
            <p class="text-muted mb-4 fs-5">
                <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($recipe['author']); ?> 
                <i class="fas fa-tag mx-2"></i><?php echo htmlspecialchars($recipe['category']); ?> 
                <i class="fas fa-eye mx-2"></i><?php echo number_format($recipe['views']); ?> views
            </p>
            
            <!-- Ingredients and Instructions side-by-side -->
            <div class="row mb-5">
                <!-- Ingredients column -->
                <div class="col-md-6">
                    <h3><i class="fas fa-shopping-basket text-success"></i> Ingredients</h3>
                    <div class="bg-light p-4 rounded shadow-sm">
                        <!-- Display ingredients with line breaks -->
                        <?php echo nl2br(htmlspecialchars($recipe['ingredients'] ?? 'No ingredients listed')); ?>
                    </div>
                </div>
                <!-- Instructions column -->
                <div class="col-md-6">
                    <h3><i class="fas fa-list-ol text-primary"></i> Instructions</h3>
                    <div class="bg-light p-4 rounded shadow-sm">
                        <!-- Display instructions with line breaks -->
                        <?php echo nl2br(htmlspecialchars($recipe['instructions'] ?? 'No instructions')); ?>
                    </div>
                </div>
            </div>

            <!-- Comments Section Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <!-- Comments header with count -->
                    <h5><i class="fas fa-comments"></i> Comments (<?php echo $commentCount; ?>)</h5>
                </div>
                <div class="card-body">
                    <!-- Comments container - loaded via AJAX -->
                    <div id="comments-list">
                        <!-- Comments load here via AJAX -->
                        <div class="text-center p-4">
                            <!-- Loading spinner -->
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Comment Form (only for logged-in users) -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <!-- Comment form header -->
                    <h6 class="mb-0"><i class="fas fa-comment-dots text-primary"></i> Add your comment</h6>
                </div>
                <div class="card-body">
                    <!-- Comment form feedback area -->
                    <div id="comment-message"></div>
                    <!-- Comment submission form -->
                    <form id="commentForm">
                        <!-- Comment textarea -->
                        <textarea name="comment" class="form-control mb-3" rows="3" 
                                  placeholder="Share your cooking experience..." required></textarea>
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Rating dropdown -->
                                <select name="rating" class="form-select mb-2" required>
                                    <option value="">Choose rating</option>
                                    <option value="1">⭐ Terrible</option>
                                    <option value="2">⭐⭐ Poor</option>
                                    <option value="3">⭐⭐⭐ Average</option>
                                    <option value="4">⭐⭐⭐⭐ Good</option>
                                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <!-- Hidden recipe ID and submit button -->
                                <input type="hidden" name="recipe_id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane"></i> Post Comment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <!-- Login prompt for guests -->
            <div class="alert alert-info">
                <i class="fas fa-sign-in-alt me-2"></i>
                <a href="login.php" class="alert-link">Login</a> to comment and rate!
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (4/12 width on large screens) -->
        <div class="col-lg-4">
            <!-- Sticky recipe stats card -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body border-bottom">
                    <!-- Recipe statistics header -->
                    <h6 class="card-title mb-3">📊 Recipe Stats</h6>
                    <!-- Stats list -->
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-tag text-info me-2"></i><?php echo htmlspecialchars($recipe['category']); ?></li>
                        <li><i class="fas fa-user text-success me-2"></i><?php echo htmlspecialchars($recipe['author']); ?></li>
                        <li><i class="fas fa-eye text-secondary me-2"></i><?php echo number_format($recipe['views']); ?> views</li>
                        <!-- Formatted creation date -->
                        <li><i class="fas fa-calendar text-muted me-2"></i><?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery document ready script for AJAX functionality -->
<script>
$(document).ready(function() {
    // Load comments on page load using recipe ID
    const recipeId = <?php echo $id; ?>;
    $('#comments-list').load('ajax/load_comments.php?recipe_id=' + recipeId, function() {
        $('.spinner-border').remove();
    });
    
    // Handle comment form submission
    $('#commentForm').submit(function(e) {
        e.preventDefault();
        // AJAX POST to add comment
        $.post('ajax/add_comment.php', $(this).serialize(), function(data) {
            if(data.success) {
                // Reload comments list on success
                $('#comments-list').load('ajax/load_comments.php?recipe_id=' + recipeId);
                // Reset form
                $('#commentForm')[0].reset();
                // Show success message
                $('#comment-message').html('<div class="alert alert-success">Comment posted! ✅</div>');
                setTimeout(() => $('#comment-message .alert').fadeOut(), 3000);
            } else {
                // Show error message
                $('#comment-message').html('<div class="alert alert-danger">' + (data.error || 'Error') + '</div>');
            }
        }, 'json').fail(function() {
            // Handle network errors
            $('#comment-message').html('<div class="alert alert-danger">Network error. Try again.</div>');
        });
    });
});
</script>

<!-- Include common footer -->
<?php include 'includes/footer.php'; ?>