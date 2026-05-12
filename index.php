<?php 
// Start PHP session to track user login state
session_start(); 
// Include database configuration and connection
require 'config/database.php'; 

// Get recipes (published only) - Fetch latest 12 published recipes with user and category info
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
    <!-- Set character encoding and responsive viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Platform - Home</title>
    
    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 🔥 FIXED: CSS PATH FROM ROOT - Custom styles from root directory -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Fixed top navigation bar with dark theme -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Brand/logo with icon -->
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-utensils me-2"></i>Recipe Platform
            </a>
            
            <!-- Mobile menu toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Main navigation menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left side nav links -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
                <!-- Right side user menu -->
                <ul class="navbar-nav ms-auto">
                    <?php if(isLoggedIn()): ?>
                        <!-- User dropdown for logged-in users -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <!-- User avatar icon and name -->
                                <i class="fas fa-user-circle me-1"></i><?=getUsername()?>
                                <?php if(isAdmin()): ?>
                                    <!-- Admin badge -->
                                    <span class="badge bg-warning text-dark ms-1">🛡️ ADMIN</span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown menu options -->
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="creator/dashboard.php">Create Recipe</a></li>
                                <?php if(isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <!-- Admin panel link -->
                                    <li><a class="dropdown-item text-warning fw-bold" href="admin/dashboard.php">🛡️ Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register links for guests -->
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

    <!-- Main content area with top padding for fixed navbar -->
    <main style="padding-top: 100px;">
        <div class="container">
            <!-- Hero Section - Main landing section -->
            <section class="text-center py-5 mb-5 fade-in-up">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <!-- Hero title with fire icon -->
                        <h1 class="display-3 fw-bold mb-4 hero-title">
                            <i class="fas fa-fire text-danger me-3"></i>
                            Delicious Recipes
                        </h1>
                        <!-- Hero subtitle -->
                        <p class="lead fs-4 text-muted mb-4">
                            Discover & share amazing recipes from our community
                        </p>
                        <!-- Hero call-to-action buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <?php if(isLoggedIn()): ?>
                                <!-- Create recipe button for logged-in users -->
                                <a href="creator/dashboard.php" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-plus me-2"></i>Create Recipe
                                </a>
                            <?php else: ?>
                                <!-- Get started button for guests -->
                                <a href="register.php" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-user-plus me-2"></i>Get Started
                                </a>
                            <?php endif; ?>
                            <!-- Browse recipes button -->
                            <a href="#recipes" class="btn btn-outline-light btn-lg px-5">Browse Recipes</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Box Section -->
            <section class="row mb-5">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4">
                            <!-- Search form submits to search.php -->
                            <form method="GET" action="search.php" class="row g-3">
                                <div class="col-md-9">
                                    <!-- Search input with icon -->
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <!-- Search query input - preserves value from GET param -->
                                        <input type="search" name="q" class="form-control border-start-0 ps-0" 
                                               placeholder="Search for recipes... (pasta, chocolate, cake)" 
                                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Search submit button -->
                                    <button class="btn btn-primary w-100 h-100" type="submit">
                                        <i class="fas fa-search me-1"></i>Find Recipes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recipes Grid Section -->
            <section id="recipes">
                <!-- Section header -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="mb-4 text-center">
                            <i class="fas fa-book-open text-primary me-3"></i>
                            Latest Recipes
                        </h2>
                    </div>
                </div>
                
                <!-- Recipes container grid -->
                <div class="row g-4">
                    <?php if(empty($recipes)): ?>
                        <!-- Empty state when no recipes exist -->
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-utensils fa-4x text-muted mb-4"></i>
                                <h3 class="text-muted mb-4">No recipes yet</h3>
                                <?php if(isLoggedIn()): ?>
                                    <!-- CTA for logged-in users -->
                                    <a href="creator/dashboard.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Be the first to share!
                                    </a>
                                <?php else: ?>
                                    <!-- CTA for guests -->
                                    <a href="register.php" class="btn btn-outline-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Join to create recipes
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Loop through recipes and display cards -->
                        <?php foreach($recipes as $recipe): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <!-- Individual recipe card -->
                            <div class="card recipe-card h-100 shadow-lg border-0 overflow-hidden">
                                <?php if(!empty($recipe['image_path'])): ?>
                                <!-- Recipe image if available -->
                                <div class="card-img-wrapper">
                                    <img src="uploads/images/<?=htmlspecialchars($recipe['image_path'])?>" 
                                         class="card-img-top" alt="<?=$recipe['title']?>"
                                         loading="lazy">
                                </div>
                                <?php else: ?>
                                <!-- Placeholder if no image -->
                                <div class="card-img-placeholder bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-utensils fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Card body content -->
                                <div class="card-body p-4">
                                    <!-- Category and rating badges -->
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-primary px-3 py-2">
                                            <?php echo htmlspecialchars($recipe['category']); ?>
                                        </span>
                                        <span class="badge bg-success">
                                            <?=($recipe['rating_avg'] ?? 0)?>⭐
                                        </span>
                                    </div>
                                    
                                    <!-- Recipe title -->
                                    <h5 class="card-title fw-bold mb-3 lh-sm">
                                        <?=htmlspecialchars($recipe['title'])?>
                                    </h5>
                                    
                                    <!-- Truncated description -->
                                    <p class="card-text text-muted small mb-3 lh-sm">
                                        <?=htmlspecialchars(substr($recipe['description'], 0, 120))?>...
                                    </p>
                                    
                                    <!-- Author and views info -->
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
                                    
                                    <!-- View recipe button -->
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

    <!-- Footer section -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- Footer brand section -->
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Recipe Platform</h5>
                    <p class="text-muted">Share your favorite recipes with the world!</p>
                </div>
                <!-- Quick links column -->
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <?php if(isLoggedIn()): ?>
                        <li><a href="creator/dashboard.php" class="text-muted text-decoration-none">Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- Account links column -->
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

    <!-- Bootstrap JavaScript bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Smooth scroll functionality for anchor links
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