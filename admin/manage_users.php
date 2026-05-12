<?php 
// Include database configuration
require '../config/database.php'; 
// Include utility functions
require '../includes/functions.php';
// Ensure only admins can access this page
requireAdmin();
?>

<!-- Include page header (navigation, CSS, etc.) -->
<?php include '../includes/header.php'; ?>

<!-- Main container -->
<div class="container">
    <!-- Page title with emoji -->
    <h2>👥 Manage Users</h2>
    
    <!-- Responsive table wrapper for mobile -->
    <div class="table-responsive">
        <table class="table table-striped">
            <!-- Table header -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <!-- Table body -->
            <tbody>
                <?php
                // Fetch all users ordered by creation date (newest first)
                $stmt = $pdo->query("SELECT * FROM dbproj_users ORDER BY created_at DESC");
                // Loop through each user record
                while($user = $stmt->fetch()):
                ?>
                <!-- User row -->
                <tr>
                    <!-- User ID (with null coalescing fallback - FIXED) -->
                    <td><?= $user['id'] ?? 'N/A' ?></td>                           
                    <!-- Username with XSS protection -->
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <!-- Email with XSS protection -->
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <!-- Role badge with dynamic styling -->
                    <td>
                        <span class="badge bg-<?= $user['role']=='admin' ? 'danger' : ($user['role']=='creator' ? 'warning' : 'secondary') ?>">
                            <?= ucfirst($user['role']) ?> <!-- Capitalize role name -->
                        </span>
                    </td>
                    <!-- Formatted join date -->
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    <!-- Action buttons (only for non-admin users) -->
                    <td>
                        <?php if($user['role'] != 'admin'): ?>
                        <!-- Delete link with confirmation dialog -->
                        <a href="delete_user.php?id=<?= $user['id'] ?? $user['user_id'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this user?')">
                           Delete
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include page footer (scripts, closing tags, etc.) -->
<?php include '../includes/footer.php'; ?>