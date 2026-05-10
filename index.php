<?php 
session_start(); 
require 'config/database.php'; 

// Get recipes (published only)
$stmt = $pdo->query("
    SELECT r.*, u.username, c.name as category 
    FROM dbproj_recipes r 
    JOIN dbproj_users u ON r.user_id = u.id 
    JOIN dbproj_categories c ON r.category_id = c.id 
    WHERE r.status = 'published' 
    ORDER BY r.created_at DESC 
    LIMIT 12
");
$recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Platform - Home</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 🔥 FIXED: CSS PATH FROM ROOT -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-utensils me-2"></i>Recipe Platform
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?=getUsername()?>
                                <?php if(isAdmin()): ?>
                                    <span class="badge bg-warning text-dark ms-1">🛡️ ADMIN</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="creator/dashboard.php">Create Recipe</a></li>
                                <?php if(isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning fw-bold" href="admin/dashboard.php">🛡️ Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 100px;">
        <div class="container">
            <!-- Hero Section -->
            <section class="text-center py-5 mb-5 fade-in-up">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h1 class="display-3 fw-bold mb-4 hero-title">
                            <i class="fas fa-fire text-danger me-3"></i>
                            Delicious Recipes
                        </h1>
                        <p class="lead fs-4 text-muted mb-4">
                            Discover & share amazing recipes from our community
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <?php if(isLoggedIn()): ?>
                                <a href="creator/dashboard.php" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-plus me-2"></i>Create Recipe
                                </a>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-user-plus me-2"></i>Get Started
                                </a>
                            <?php endif; ?>
                            <a href="#recipes" class="btn btn-outline-light btn-lg px-5">Browse Recipes</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Box -->
            <section class="row mb-5">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4">
                            <form method="GET" action="search.php" class="row g-3">
                                <div class="col-md-9">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="search" name="q" class="form-control border-start-0 ps-0" 
                                               placeholder="Search for recipes... (pasta, chocolate, cake)" 
                                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100 h-100" type="submit">
                                        <i class="fas fa-search me-1"></i>Find Recipes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recipes Grid -->
            <section id="recipes">
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="mb-4 text-center">
                            <i class="fas fa-book-open text-primary me-3"></i>
                            Latest Recipes
                        </h2>
                    </div>
                </div>
                
                <div class="row g-4">
                    <?php if(empty($recipes)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-utensils fa-4x text-muted mb-4"></i>
                                <h3 class="text-muted mb-4">No recipes yet</h3>
                                <?php if(isLoggedIn()): ?>
                                    <a href="creator/dashboard.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Be the first to share!
                                    </a>
                                <?php else: ?>
                                    <a href="register.php" class="btn btn-outline-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Join to create recipes
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($recipes as $recipe): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card recipe-card h-100 shadow-lg border-0 overflow-hidden">
                                <?php if(!empty($recipe['image_path'])): ?>
                                <div class="card-img-wrapper">
                                    <img src="uploads/images/<?=htmlspecialchars($recipe['image_path'])?>" 
                                         class="card-img-top" alt="<?=$recipe['title']?>"
                                         loading="lazy">
                                </div>
                                <?php else: ?>
                                <div class="card-img-placeholder bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-utensils fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="card-body p-4">
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-primary px-3 py-2">
                                            <?php echo htmlspecialchars($recipe['category']); ?>
                                        </span>
                                        <span class="badge bg-success">
                                            <?=($recipe['rating_avg'] ?? 0)?>⭐
                                        </span>
                                    </div>
                                    
                                    <h5 class="card-title fw-bold mb-3 lh-sm">
                                        <?=htmlspecialchars($recipe['title'])?>
                                    </h5>
                                    
                                    <p class="card-text text-muted small mb-3 lh-sm">
                                        <?=htmlspecialchars(substr($recipe['description'], 0, 120))?>...
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?=htmlspecialchars($recipe['username'])?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i>
                                            <?=number_format($recipe['views'] ?? 0)?>
                                        </small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="recipe.php?id=<?=$recipe['id']?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-book-open me-1"></i>
                                            View Recipe
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Recipe Platform</h5>
                    <p class="text-muted">Share your favorite recipes with the world!</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <?php if(isLoggedIn()): ?>
                        <li><a href="creator/dashboard.php" class="text-muted text-decoration-none">Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Account</h6>
                    <ul class="list-unstyled">
                        <?php if(isLoggedIn()): ?>
                        <li><a href="logout.php" class="text-muted text-decoration-none">Logout</a></li>
                        <?php else: ?>
                        <li><a href="login.php" class="text-muted text-decoration-none">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>