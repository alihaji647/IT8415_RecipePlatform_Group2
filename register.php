<?php
// Include database configuration and PDO connection
require 'config/database.php';
// Initialize empty message variable for form feedback
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and hash form inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Basic username validation (minimum 3 characters)
    if (strlen($username) < 3) {
        $message = '<div class="alert alert-danger">Username must be 3+ chars</div>';
    } else {
        try {
            // Insert new user into database
            $stmt = $pdo->prepare("INSERT INTO dbproj_users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            // Success message with login link
            $message = '<div class="alert alert-success">✅ Account created! <a href="login.php">Login now</a></div>';
        } catch (PDOException $e) {
            // Handle duplicate username/email errors
            $message = '<div class="alert alert-danger">❌ Username/Email already exists</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title -->
    <title>Register</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Light background body -->
<body class="bg-light">
<!-- Main container with top margin -->
<div class="container mt-5">
    <!-- Center the registration card -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Registration card with shadow -->
            <div class="card shadow">
                <div class="card-body">
                    <!-- Registration header -->
                    <h3 class="text-center mb-4">📝 Register</h3>
                    <!-- Display success/error message -->
                    <?php echo $message; ?>
                    
                    <!-- Registration form -->
                    <form method="POST">
                        <!-- Username input -->
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required minlength="3">
                        </div>
                        <!-- Email input -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <!-- Password input -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <!-- Submit button -->
                        <button class="btn btn-success w-100">Create Account</button>
                    </form>
                    <!-- Login link for existing users -->
                    <p class="text-center mt-3">
                        Have account? <a href="login.php">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>