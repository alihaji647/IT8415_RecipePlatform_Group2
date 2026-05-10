<?php
session_start();
require 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM dbproj_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // 🔥 PRODUCTION: Admin auto-redirect
        if (getUserRole() === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $message = '<div class="alert alert-danger">❌ Invalid username/password</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Recipe Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h3>🔐 Welcome Back</h3>
                    </div>
                    
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control form-control-lg" required 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <button class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-1">Don't have an account?</p>
                        <a href="register.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </div>
                    
                    <!-- 🔥 Production hint (remove later) -->
                    <div class="alert alert-info mt-4 small">
                        <strong>Admin:</strong> admin1 / password
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>