<?php 
require 'config/database.php'; 
$q = trim($_GET['q'] ?? '');
$results = [];

if (!empty($q)) {
    $stmt = $pdo->prepare("
        SELECT r.*, c.name as category, u.username 
        FROM dbproj_recipes r 
        JOIN dbproj_categories c ON r.category_id = c.id
        JOIN dbproj_users u ON r.user_id = u.id
        WHERE MATCH(r.title, r.description) AGAINST(? IN BOOLEAN MODE)
        AND r.status = 'published'
    ");
    $stmt->execute([$q]);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results - Recipe Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Same navbar as index.php -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-utensils me-2"></i>Recipes</a>
            <div class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['username'])): ?>
                    <span class="navbar-text me-3">👋 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a class="nav-link" href="creator/dashboard.php">Create</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <a href="index.php" class="btn btn-outline-primary mb-3">&larr; Back to Home</a>
        
        <h2>🔍 Search Results</h2>
        <h5>"<?php echo htmlspecialchars($q); ?>" (<?php echo count($results); ?> found)</h5>
        
        <?php if(empty($results)): ?>
            <div class="alert alert-info text-center p-5">
                <i class="fas fa-search fa-3x mb-3 opacity-75"></i>
                <h4>No recipes found</h4>
                <p>Try searching: <strong>chocolate</strong>, <strong>pasta</strong>, or <strong>cake</strong></p>
                <a href="index.php" class="btn btn-primary">Browse All Recipes</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($results as $recipe): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body">
                            <span class="badge bg-success mb-2"><?php echo htmlspecialchars($recipe['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text"><?php echo substr($recipe['description'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between">
                                <small><i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['username']); ?></small>
                                <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-outline-primary btn-sm">View Recipe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
    .hover-shadow:hover { transform: translateY(-5px); transition: all 0.3s; }
    </style>
</body>
</html>