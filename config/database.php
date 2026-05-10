<?php
// 🔥 NO session_start() here - handled in login/header

$host = 'localhost';
$db = 'recipe_platform';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// 🔥 FIXED: Handles BOTH id AND user_id columns
function getUserRole() {
    if(!isset($_SESSION['user_id'])) {
        return 'guest';
    }
    
    // Use cached session role if available
    if(isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    
    global $pdo;
    try {
        // 🔥 TRY id first, then user_id
        $stmt = $pdo->prepare("SELECT role FROM dbproj_users WHERE id = ? OR user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $role = $stmt->fetchColumn();
        
        // Cache in session
        $_SESSION['role'] = $role ?: 'user';
        return $_SESSION['role'];
    } catch(Exception $e) {
        error_log("Role lookup failed: " . $e->getMessage());
        return 'user';
    }
}

function getUsername() {
    if(!isset($_SESSION['user_id'])) return 'Guest';
    if(isset($_SESSION['username'])) return $_SESSION['username'];
    
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT username FROM dbproj_users WHERE id = ? OR user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $username = $stmt->fetchColumn();
        $_SESSION['username'] = $username ?: 'Guest';
        return $_SESSION['username'];
    } catch(Exception $e) {
        return 'Guest';
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return getUserRole() === 'admin';
}
?>