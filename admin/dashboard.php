<?php 
session_start();
require '../config/database.php'; 

// 🔥 ADMIN CHECK
if(!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ../login.php?error=admin');
    exit();
}

// Safe stats function
function getSafeCount($pdo, $query) {
    try {
        return $pdo->query($query)->fetchColumn() ?: 0;
    } catch (Exception $e) {
        return 0;
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success border-start border-5 border-primary">
                <i class="fas fa-shield-alt me-2"></i>
                Welcome, <strong><?=getUsername()?></strong>! 🛡️ Admin Dashboard
            </div>
            
            <h2 class="mb-4">📊 Dashboard Overview</h2>
            
            <!-- Stats Cards -->
            <div class="row mb-4 g-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary text-center h-100 recipe-card">
                        <div class="card-body">
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_users")?></h3>
                            <p class="mb-0"><i class="fas fa-users"></i> Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success text-center h-100 recipe-card">
                        <div class="card-body">
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_recipes")?></h3>
                            <p class="mb-0"><i class="fas fa-utensils"></i> Total Recipes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning text-center h-100 recipe-card">
                        <div class="card-body">
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT AVG(rating_avg) FROM dbproj_recipes WHERE rating_avg >= 4.5")*10?></h3>
                            <p class="mb-0"><i class="fas fa-star"></i> Top Rated</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger text-center h-100 recipe-card">
                        <div class="card-body">
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_recipes WHERE status='draft'")?></h3>
                            <p class="mb-0"><i class="fas fa-eye-slash"></i> Drafts</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Recipes -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Recipes</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="80">#</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Views</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            // 🔥 PERFECT QUERY FOR YOUR TABLE
                                            $stmt = $pdo->query("
                                                SELECT 
                                                    r.id, 
                                                    r.title, 
                                                    r.description,
                                                    r.status,
                                                    r.views,
                                                    r.rating_avg,
                                                    u.username,
                                                    r.created_at
                                                FROM dbproj_recipes r 
                                                LEFT JOIN dbproj_users u ON r.user_id = u.id 
                                                ORDER BY r.created_at DESC 
                                                LIMIT 10
                                            ");
                                            
                                            if($stmt->rowCount() === 0) {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-utensils fa-3x mb-3 opacity-50 d-block"></i>
                                                <h5>No recipes yet</h5>
                                                <a href="../add_recipe.php" class="btn btn-primary">Create First Recipe</a>
                                            </td>
                                        </tr>
                                        <?php } else {
                                            while($recipe = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <tr class="align-middle">
                                            <td>
                                                <strong>#<?=$recipe['id']?></strong>
                                            </td>
                                            <td>
                                                <strong><?=htmlspecialchars(substr($recipe['title'], 0, 40))?></strong>
                                                <?php if(strlen($recipe['title']) > 40) echo '<small class="text-muted">...</small>'; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?=empty($recipe['username']) ? 'bg-secondary' : 'bg-info'?>">
                                                    <?=htmlspecialchars($recipe['username'] ?? 'Unknown')?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?= $recipe['status'] === 'published' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= ucfirst($recipe['status']) ?>
                                                </span>
                                            </td>
                                            <td><strong><?=number_format($recipe['views'] ?? 0)?></strong></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="../recipe.php?id=<?=$recipe['id']?>" 
                                                       class="btn btn-outline-primary" target="_blank" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-success" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" onclick="confirmDelete(<?=$recipe['id']?>)" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                            }
                                        }
                                        } catch(PDOException $e) {
                                            echo '<tr><td colspan="6" class="text-center bg-danger text-white py-3">';
                                            echo 'Database Error: ' . $e->getMessage();
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="manage_users.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-users text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Manage Users</div>
                                    <small class="text-muted">Edit, ban, promote users</small>
                                </div>
                            </a>
                            <a href="manage_recipes.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-utensils text-success me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Manage Recipes</div>
                                    <small class="text-muted">Approve, delete recipes</small>
                                </div>
                            </a>
                            <a href="reports.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-flag text-warning me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">View Reports</div>
                                    <small class="text-muted">Handle user reports</small>
                                </div>
                            </a>
                            <a href="../index.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-globe text-info me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">View Live Site</div>
                                </div>
                            </a>
                            <a href="../logout.php" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
                                <i class="fas fa-sign-out-alt me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Logout</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if(confirm(`Delete recipe #${id}? This cannot be undone.`)) {
        fetch(`delete_recipe.php?id=${id}`, {method: 'DELETE'})
            .then(() => location.reload());
    }
}
</script>

<?php include '../includes/footer.php'; ?>