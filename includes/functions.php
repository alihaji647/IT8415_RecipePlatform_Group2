<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAdmin() {
    if (!isLoggedIn() || getUserRole() != 'admin') {
        header('Location: ../login.php');
        exit;
    }
}
?>
