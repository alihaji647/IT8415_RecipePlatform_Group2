<?php
require 'config/database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    if (strlen($username) < 3) {
        $message = '<div class="alert alert-danger">Username must be 3+ chars</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO dbproj_users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            $message = '<div class="alert alert-success">✅ Account created! <a href="login.php">Login now</a></div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">❌ Username/Email already exists</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">📝 Register</h3>
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required minlength="3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <button class="btn btn-success w-100">Create Account</button>
                    </form>
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