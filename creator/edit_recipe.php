<?php 
// Start session for authentication
session_start();
// Include database connection
require '../config/database.php'; 

// Creator/Admin only access
if (!isset($_SESSION['user_id']) || !in_array(getUserRole(), ['creator', 'admin'])) {
    // Redirect unauthorized users
    header('Location: ../login.php');
    exit;
}

// Sanitize recipe ID
$id = (int)($_GET['id'] ?? 0);
$recipe = null;

// Fetch recipe if valid ID (ownership check)
if($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM dbproj_recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $recipe = $stmt->fetch();
}

// Security: Redirect if recipe not found on POST
if (!$recipe && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: dashboard.php?error=not_found');
    exit;
}

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $recipe) {
    // Sanitize form inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $category_id = (int)$_POST['category_id'];
    
    // Keep existing image or upload new one
    $image_path = $recipe['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Generate secure filename
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = '../uploads/images/' . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = $image_name;
        }
    }
    
    // Update recipe (ownership verified)
    $stmt = $pdo->prepare("
        UPDATE dbproj_recipes SET 
        title=?, description=?, ingredients=?, instructions=?, 
        category_id=?, image_path=? 
        WHERE id=? AND user_id=?
    ");
    $stmt->execute([
        $title, $description, $ingredients, $instructions,
        $category_id, $image_path, $id, $_SESSION['user_id']
    ]);
    
    // Redirect with success flag
    header('Location: dashboard.php?updated=1');
    exit;
}

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM dbproj_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title with recipe name -->
    <title>Edit Recipe</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <!-- Back to dashboard -->
            <a class="navbar-brand" href="dashboard.php">← Dashboard</a>
        </div>
    </nav>

    <!-- Main container -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Dynamic page header -->
                <h2>Edit Recipe: <?php echo htmlspecialchars($recipe['title'] ?? 'New Recipe'); ?></h2>
                
                <!-- Error message from query params -->
                <?php if(!empty($_GET['error'])): ?>
                <div class="alert alert-danger">Recipe not found</div>
                <?php endif; ?>

                <?php if($recipe): ?>
                <!-- Edit form with file upload -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Title input (pre-populated) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                    </div>
                    
                    <!-- Category + Status row -->
                    <div class="row">
                        <!-- Category dropdown -->
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $recipe['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Readonly status display -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst($recipe['status']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Description textarea -->
                    <div class="mb-3 mt-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                    </div>

                    <!-- Ingredients + Instructions (two-column) -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Ingredients</label>
                            <textarea name="ingredients" class="form-control" rows="6"><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instructions</label>
                            <textarea name="instructions" class="form-control" rows="10"><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                        </div>
                    </div>

                    <!-- Image upload with preview -->
                    <div class="mb-4">
                        <label class="form-label">Image (leave empty to keep current)</label>
                        <?php if($recipe['image_path']): ?>
                        <!-- Current image preview -->
                        <div class="mb-2">
                            <img src="../uploads/images/<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                 class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        <?php endif; ?>
                        <!-- New image upload (optional) -->
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <!-- Form buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-end">
                        <!-- Cancel button -->
                        <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <!-- Update button -->
                        <button type="submit" class="btn btn-primary">Update Recipe</button>
                    </div>
                </form>
                <?php else: ?>
                <!-- Recipe not found message -->
                <div class="alert alert-warning">Recipe not found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>