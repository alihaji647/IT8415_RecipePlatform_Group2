<?php 
session_start();
require '../config/database.php'; 

if (!isset($_SESSION['user_id']) || getUserRole() !== 'creator') {
    header('Location: ../login.php');
    exit;
}

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $category_id = (int)$_POST['category_id'];
    
    if (strlen($title) < 3) {
        $error = 'Title must be at least 3 characters';
    } elseif (!$category_id) {
        $error = 'Please select a category';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload an image';
    } else {
        // Handle file upload
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = '../uploads/images/' . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Save recipe
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO dbproj_recipes 
                    (title, description, ingredients, instructions, image_path, user_id, category_id, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'draft')
                ");
                $stmt->execute([
                    $title, $description, $ingredients, $instructions,
                    $image_name, $_SESSION['user_id'], $category_id
                ]);
                $success = 'Recipe created successfully! <a href="dashboard.php">View Dashboard</a>';
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Failed to upload image';
        }
    }
}

// Get categories
$categories = $pdo->query("SELECT * FROM dbproj_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Recipe - Creator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="mb-4"><i class="fas fa-plus-circle text-success me-2"></i>Add New Recipe</h2>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Recipe Title *</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" 
                                        placeholder="A short description of your recipe"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category *</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Choose category</option>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="6" 
                                        placeholder="• 2 cups flour&#10;• 1 tsp salt&#10;• 1 cup sugar"><?php echo htmlspecialchars($_POST['ingredients'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Instructions</label>
                                <textarea name="instructions" class="form-control" rows="10" 
                                        placeholder="1. Preheat oven to 350°F&#10;2. Mix ingredients..."><?php echo htmlspecialchars($_POST['instructions'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Recipe Image *</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="form-text text-muted">JPG, PNG up to 5MB</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>