<?php 
session_start();
require '../config/database.php'; 

if (!isset($_SESSION['user_id']) || getUserRole() !== 'creator') {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, c.name as category 
    FROM dbproj_recipes r 
    LEFT JOIN dbproj_categories c ON r.category_id = c.id
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$recipes = $stmt->fetchAll();

$page_title = "Creator Dashboard - My Recipes";
?>
<?php include '../includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit text-primary me-2"></i>My Recipes</h2>
                <a href="add_recipe.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>Add New Recipe
                </a>
            </div>

            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>Recipe saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(empty($recipes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-5x text-muted mb-4"></i>
                <h3 class="text-muted">No recipes yet</h3>
                <p class="text-muted">Get started by creating your first recipe!</p>
                <a href="add_recipe.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Create First Recipe
                </a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
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
                        <?php foreach($recipes as $recipe): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($recipe['title']); ?></strong>
                                <br><small><?php echo substr($recipe['description'], 0, 50); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($recipe['category'] ?? 'Uncategorized'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $recipe['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($recipe['status']); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($recipe['views'] ?? 0); ?></td>
                            <td><?php echo date('M j', strtotime($recipe['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if($recipe['status'] === 'draft'): ?>
                                    <a href="publish.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-outline-success" 
                                       onclick="return confirm('Publish this recipe?')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
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

<?php include '../includes/footer.php'; ?>