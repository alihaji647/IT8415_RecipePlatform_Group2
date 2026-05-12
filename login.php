<?php
// Start PHP session for user authentication
session_start();
// Include database configuration and PDO connection
require 'config/database.php';

// Initialize empty message variable for login feedback
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Prepare and execute query to find user by username
    $stmt = $pdo->prepare("SELECT * FROM dbproj_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Verify user exists and password matches (using PHP's password_verify)
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables for authenticated user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // 🔥 PRODUCTION: Admin auto-redirect - Redirect admins to admin dashboard, others to home
        if (getUserRole() === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        // Display error message for invalid credentials
        $message = '<div class="alert alert-danger">❌ Invalid username/password</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title -->
    <title>Login - Recipe Platform</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<!-- Main container with top margin -->
<div class="container mt-5">
    <!-- Center the login card -->
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <!-- Login card with shadow and no border -->
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Login header with icon and title -->
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h3>🔐 Welcome Back</h3>
                    </div>
                    
                    <!-- Display login error/success message -->
                    <?php echo $message; ?>
                    
                    <!-- Login form -->
                    <form method="POST">
                        <!-- Username input field -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Username</label>
                            <!-- Preserve username value on failed login attempt -->
                            <input type="text" name="username" class="form-control form-control-lg" required 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <!-- Password input field -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <!-- Login submit button -->
                        <button class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    
                    <!-- Register link for new users -->
                    <div class="text-center mt-4">
                        <p class="mb-1">Don't have an account?</p>
                        <a href="register.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </div>
                    
                    <!-- 🔥 Production hint (remove later) - Default admin credentials -->
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