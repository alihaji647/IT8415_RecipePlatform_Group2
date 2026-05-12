<?php
// Fallback functions for basic authentication (defensive programming)
// Only define if not already loaded from main functions.php

if (!function_exists('isLoggedIn')) {
    /**
     * Check if user is logged in
     * @return bool True if user_id exists in session
     */
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('getUserRole')) {
    /**
     * Get current user role from session
     * @return string User role or empty string
     */
    function getUserRole() {
        return $_SESSION['role'] ?? '';
    }
}

if (!function_exists('requireAdmin')) {
    /**
     * Require admin access - redirects if not admin
     * Automatically starts session if needed
     */
    function requireAdmin() {
        // Start session if not already started
        session_start();
        // Check login status AND admin role
        if (!isLoggedIn() || getUserRole() != 'admin') {
            // Redirect to login with no error exposure
            header('Location: ../login.php');
            exit;
        }
    }
}
?>