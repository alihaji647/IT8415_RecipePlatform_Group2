<?php
// Start the current session
session_start();
// Destroy all session data (logout user)
session_destroy();
// Redirect to home page after logout
header("Location: index.php");
// Stop script execution after redirect
exit;
?>