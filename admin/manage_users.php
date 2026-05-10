<?php 
require '../config/database.php'; 
require '../includes/functions.php';
requireAdmin();
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>👥 Manage Users</h2>
    
    <div class="table-responsive">
        <table class="table table-striped">
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
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM dbproj_users ORDER BY created_at DESC");
                while($user = $stmt->fetch()):
                ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= $user['role']=='admin' ? 'danger' : ($user['role']=='creator' ? 'warning' : 'secondary') ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <?php if($user['role'] != 'admin'): ?>
                        <a href="delete_user.php?id=<?= $user['user_id'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

