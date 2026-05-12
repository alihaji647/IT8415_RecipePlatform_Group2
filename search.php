<?php 
// Include database configuration and PDO connection
require 'config/database.php'; 

// Get and sanitize search query from URL parameter
$q = trim($_GET['q'] ?? '');
// Initialize empty results array
$results = [];

if (!empty($q)) {
    // Full-text search on title and description using MySQL MATCH AGAINST
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
    <!-- Page title with search context -->
    <title>Search Results - Recipe Platform</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<!-- Light background body -->
<body class="bg-light">
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Brand link to home -->
            <a class="navbar-brand" href="index.php"><i class="fas fa-utensils me-2"></i>Recipes</a>
            <!-- Right-aligned navigation items -->
            <div class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['username'])): ?>
                    <!-- Welcome message for logged-in users -->
                    <span class="navbar-text me-3">👋 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <!-- Create recipe link -->
                    <a class="nav-link" href="creator/dashboard.php">Create</a>
                    <!-- Logout link -->
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <!-- Login link for guests -->
                    <a class="nav-link" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main content container -->
    <div class="container mt-4">
        <!-- Back to home button -->
        <a href="index.php" class="btn btn-outline-primary mb-3">&larr; Back to Home</a>
        
        <!-- Search results header -->
        <h2>🔍 Search Results</h2>
        <!-- Search query display with result count -->
        <h5>"<?php echo htmlspecialchars($q); ?>" (<?php echo count($results); ?> found)</h5>
        
        <?php if(empty($results)): ?>
            <!-- No results found message -->
            <div class="alert alert-info text-center p-5">
                <i class="fas fa-search fa-3x mb-3 opacity-75"></i>
                <h4>No recipes found</h4>
                <!-- Search suggestions -->
                <p>Try searching: <strong>chocolate</strong>, <strong>pasta</strong>, or <strong>cake</strong></p>
                <!-- Browse all recipes button -->
                <a href="index.php" class="btn btn-primary">Browse All Recipes</a>
            </div>
        <?php else: ?>
            <!-- Search results grid -->
            <div class="row g-4">
                <?php foreach($results as $recipe): ?>
                <!-- Individual search result card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body">
                            <!-- Category badge -->
                            <span class="badge bg-success mb-2"><?php echo htmlspecialchars($recipe['category']); ?></span>
                            <!-- Recipe title -->
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <!-- Truncated description -->
                            <p class="card-text"><?php echo substr($recipe['description'], 0, 100); ?>...</p>
                            <!-- Author and view recipe button -->
                            <div class="d-flex justify-content-between">
                                <small><i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['username']); ?></small>
                                <!-- Link to full recipe -->
                                <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-outline-primary btn-sm">View Recipe</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Custom CSS for hover effect -->
    <style>
    .hover-shadow:hover { transform: translateY(-5px); transition: all 0.3s; }
    </style>
</body>
</html>