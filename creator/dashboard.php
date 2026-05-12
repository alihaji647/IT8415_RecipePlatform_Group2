<?php 
// Start session for creator authentication
session_start();
// Include database connection
require '../config/database.php'; 

// Creator-only access check
if (!isset($_SESSION['user_id']) || getUserRole() !== 'creator') {
    // Redirect non-creators to login
    header('Location: ../login.php');
    exit;
}

// Fetch creator's recipes with category JOIN
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category 
    FROM dbproj_recipes r 
    LEFT JOIN dbproj_categories c ON r.category_id = c.id
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
// Execute with user_id parameter (SQL injection safe)
$stmt->execute([$_SESSION['user_id']]);
// Fetch all recipes as array
$recipes = $stmt->fetchAll();

// Set page title variable for header
$page_title = "Creator Dashboard - My Recipes";
?>
<!-- Include common header (navigation, CSS, meta tags) -->
<?php include '../includes/header.php'; ?>

<!-- Main content area -->
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Header with title + Add button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit text-primary me-2"></i>My Recipes</h2>
                <!-- Add new recipe button -->
                <a href="add_recipe.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>Add New Recipe
                </a>
            </div>

            <!-- Success message from query params -->
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>Recipe saved successfully!
                <!-- Dismissible close button -->
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Empty state when no recipes -->
            <?php if(empty($recipes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-5x text-muted mb-4"></i>
                <h3 class="text-muted">No recipes yet</h3>
                <p class="text-muted">Get started by creating your first recipe!</p>
                <!-- CTA button -->
                <a href="add_recipe.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Create First Recipe
                </a>
            </div>
            <?php else: ?>
            <!-- Recipes table (responsive) -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <!-- Table header -->
                    <thead class="table-dark">
                        <tr>
                            <th>Recipe</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through user's recipes -->
                        <?php foreach($recipes as $recipe): ?>
                        <tr>
                            <!-- Recipe title + truncated description -->
                            <td>
                                <strong><?php echo htmlspecialchars($recipe['title']); ?></strong>
                                <br><small><?php echo substr($recipe['description'], 0, 50); ?>...</small>
                            </td>
                            <!-- Category name (fallback to Uncategorized) -->
                            <td><?php echo htmlspecialchars($recipe['category'] ?? 'Uncategorized'); ?></td>
                            <!-- Dynamic status badge -->
                            <td>
                                <span class="badge bg-<?php echo $recipe['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($recipe['status']); ?>
                                </span>
                            </td>
                            <!-- Formatted view count -->
                            <td><?php echo number_format($recipe['views'] ?? 0); ?></td>
                            <!-- Formatted creation date -->
                            <td><?php echo date('M j', strtotime($recipe['created_at'])); ?></td>
                            <!-- Action buttons -->
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Edit recipe -->
                                    <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Publish button (draft only) -->
                                    <?php if($recipe['status'] === 'draft'): ?>
                                    <a href="publish.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-outline-success" 
                                       onclick="return confirm('Publish this recipe?')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    <!-- Delete with confirmation -->
                                    <a href="delete.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Delete forever?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include common footer (scripts, analytics, etc.) -->
<?php include '../includes/footer.php'; ?>