<?php
// 🔥 NO session_start() here - handled in login/header files

// Database configuration (hardcoded - consider using .env in production)
$host = 'localhost';
$db = 'recipe_platform';
$user = 'root';
$pass = '';

try {
    // Create PDO connection with UTF-8 charset
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Enable exception mode for error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Fatal error on database connection failure
    die("DB Error: " . $e->getMessage());
}

// 🔥 FIXED: Handles BOTH id AND user_id columns in users table
function getUserRole() {
    // Check if user is logged in
    if(!isset($_SESSION['user_id'])) {
        return 'guest';
    }
    
    // Use cached session role if available (performance)
    if(isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    
    global $pdo;
    try {
        // 🔥 TRY id first, then user_id (handles legacy/mixed schemas)
        $stmt = $pdo->prepare("SELECT role FROM dbproj_users WHERE id = ? OR user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $role = $stmt->fetchColumn();
        
        // Cache role in session and default to 'user'
        $_SESSION['role'] = $role ?: 'user';
        return $_SESSION['role'];
    } catch(Exception $e) {
        // Log error but don't break page
        error_log("Role lookup failed: " . $e->getMessage());
        return 'user';
    }
}

function getUsername() {
    // Return cached username or 'Guest' for non-logged-in users
    if(!isset($_SESSION['user_id'])) return 'Guest';
    if(isset($_SESSION['username'])) return $_SESSION['username'];
    
    global $pdo;
    try {
        // Query username supporting both id and user_id columns
        $stmt = $pdo->prepare("SELECT username FROM dbproj_users WHERE id = ? OR user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $username = $stmt->fetchColumn();
        // Cache username with 'Guest' fallback
        $_SESSION['username'] = $username ?: 'Guest';
        return $_SESSION['username'];
    } catch(Exception $e) {
        // Graceful fallback on error
        return 'Guest';
    }
}

// Simple login check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Admin check using role function
function isAdmin() {
    return getUserRole() === 'admin';
}
?>