<?php
// 🔥 DYNAMIC PATH DETECTION - WORKS FROM ANY FOLDER
$currentPath = $_SERVER['SCRIPT_NAME'];
$isRootLevel = (strpos($currentPath, '/creator/') === false && strpos($currentPath, '/admin/') === false);
$pathPrefix = $isRootLevel ? '' : '../';

// 🔥 LOAD SHARED FUNCTIONS (if not already loaded)
if (!function_exists('isLoggedIn')) {
    require_once dirname(dirname(__FILE__)) . '/config/database.php';
}

// Use existing functions from database.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(isset($page_title)): ?>
        <title><?=$page_title?> - Recipe Platform</title>
    <?php else: ?>
        <title>Recipe Platform</title>
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 🔥 UNIVERSAL CSS -->
    <link href="<?=$pathPrefix?>css/style.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <!-- Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="/">
                <i class="fas fa-utensils me-2"></i>Recipe Platform
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active fw-bold' : ''; ?>" 
                           href="<?=$pathPrefix?>index.php">Home</a>
                    </li>
                    <?php if(function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active fw-bold' : ''; ?>" 
                           href="<?=$pathPrefix?>creator/dashboard.php">Create</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if(function_exists('isLoggedIn') && isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?=htmlspecialchars(getUsername())?>
                                <?php if(function_exists('isAdmin') && isAdmin()): ?>
                                    <span class="badge bg-warning text-dark ms-1 fs-6">🛡️ ADMIN</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="<?=$pathPrefix?>profile.php">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?=$pathPrefix?>creator/dashboard.php">
                                    <i class="fas fa-plus-circle me-2"></i>Create Recipe
                                </a></li>
                                <?php if(function_exists('isAdmin') && isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning fw-bold" href="<?=$pathPrefix?>admin/dashboard.php">
                                        <i class="fas fa-shield-alt me-2"></i>🛡️ Admin Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?=$pathPrefix?>logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$pathPrefix?>login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$pathPrefix?>register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Spacer for fixed navbar -->
    <div style="padding-top: 80px;"></div>