<?php
// 🔥 DYNAMIC CSS PATH - WORKS EVERYWHERE
require_once dirname(dirname(__FILE__)) . '/config/database.php';

$currentPath = $_SERVER['SCRIPT_NAME'];
$isRoot = strpos($currentPath, '/admin/') === false && strpos($currentPath, '/creator/') === false;
$cssPath = $isRoot ? 'css/style.css' : '../css/style.css';
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
    
    <!-- 🔥 UNIVERSAL CSS - WORKS FROM ANY FOLDER -->
    <link href="<?=$cssPath?>" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php if(isset($extra_css)): echo $extra_css; endif; ?>
</head>
<body>
    <!-- Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-glass">
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
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <?php if(isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="creator/dashboard.php">Create</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?=getUsername()?>
                                <?php if(isAdmin()): ?>
                                    <span class="badge bg-warning text-dark ms-1 fs-6">🛡️ ADMIN</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <?php if(isLoggedIn()): ?>
                                <li><a class="dropdown-item" href="creator/dashboard.php">
                                    <i class="fas fa-plus-circle me-2"></i>Create Recipe
                                </a></li>
                                <?php endif; ?>
                                <?php if(isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning fw-bold" href="admin/dashboard.php">
                                        <i class="fas fa-shield-alt me-2"></i>🛡️ Admin Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
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

    <!-- Content Spacer -->
    <div class="content-spacer"></div>

<?php
// CSS for glass navbar
$extra_css = '
<style>
.bg-glass { background: rgba(255,255,255,0.95) !important; backdrop-filter: blur(20px); }
.content-spacer { padding-top: 100px; }
@media (max-width: 991px) { .content-spacer { padding-top: 80px; } }
</style>';
?>